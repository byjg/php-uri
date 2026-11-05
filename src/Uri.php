<?php

namespace ByJG\Util;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 *
 * @psalm-consistent-constructor
 */
class Uri implements CustomUriInterface
{
    private string $host = '';
    private string $fragment = '';
    private string $path = '';
    private string $scheme = '';
    private ?int $port = null;

    /** The standard port of a scheme, which PSR-7 leaves out of the URI */
    private const array STANDARD_PORTS = ['http' => 80, 'https' => 443];

    /**
     * The query as an ordered list of raw (still percent-encoded) key/value pairs.
     * A null value means the key was given without "=" at all (e.g. "?flag").
     *
     * @var list<array{0: string, 1: string|null}>
     */
    private array $queryPairs = [];

    /**
     * Index built by parse_str(). Kept only as a fallback for getQueryPart()/hasQueryKey(),
     * so callers relying on the PHP-mangled key names (e.g. "group_id" for "group.id")
     * keep working.
     *
     * The mangled-name fallback is deprecated and removed in 8.0, with this index (see the
     * CHANGELOG): ask for the real key instead, or use getQueryParts(), which never consults it.
     * The property is private, so the deprecation is the fallback's, not the property's.
     *
     * @var array<string, mixed>
     */
    private array $query = [];

    /**
     * @throws InvalidArgumentException when the scheme is not a letter followed by letters, digits, "+", "-" or "." (RFC 3986)
     */
    #[Override]
    public function withScheme(string $scheme): UriInterface
    {
        if ($scheme !== '' && preg_match('/^[A-Za-z][A-Za-z0-9+.\-]*$/', $scheme) !== 1) {
            throw new InvalidArgumentException("Invalid scheme: $scheme");
        }
        $clone = clone $this;
        $clone->scheme = strtolower($scheme);
        return $clone;
    }

    #[Override]
    public function getScheme(): string
    {
        return $this->scheme;
    }

    private ?string $username = null;
    private ?string $password = null;

    /**
     * PSR-7: an empty user removes the user information, so the password is dropped too.
     */
    #[Override]
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->username = $user === '' ? null : $user;
        $clone->password = $user === '' ? null : $password;
        return $clone;
    }

    /**
     * The user and the password are stored decoded (getUsername()/getPassword()) and each one is
     * percent-encoded here, so a ":" or "@" in the user cannot be read as a delimiter (RFC 3986
     * section 3.2.1). The ":" is written whenever there is a password, even an empty one.
     */
    #[Override]
    public function getUserInfo(): string
    {
        return rawurlencode($this->username ?? "")
            . ($this->password !== null ? ':' . rawurlencode($this->password) : '' );
    }

    /**
     * @return string|null
     */
    #[Override]
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    #[Override]
    public function getPassword(): ?string
    {
        return $this->password;
    }

    #[Override]
    public function withHost(string $host): UriInterface
    {
        $clone = clone $this;
        $clone->host = strtolower($host);
        return $clone;
    }

    /**
     * The host in lowercase: it is case-insensitive (RFC 3986 section 3.2.2)
     */
    #[Override]
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param int|null $port
     * @return $this
     */
    #[Override]
    public function withPort(?int $port): UriInterface
    {
        $clone = clone $this;
        $clone->port = is_numeric($port) ? intval($port) : null;
        return $clone;
    }

    #[Override]
    public function getPort(): ?int
    {
        return $this->port === (self::STANDARD_PORTS[$this->scheme] ?? null) ? null : $this->port;
    }

    #[Override]
    public function withPath(string $path): UriInterface
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    /**
     * Leading slashes are reduced to one ("http://host//path" has the path "/path"), so the path cannot
     * be read as an authority (CVE-2015-3257). The string form keeps the path as it was given.
     */
    #[Override]
    public function getPath(): string
    {
        return preg_replace('~^/{2,}~', '/', $this->path) ?? $this->path;
    }

    #[Override]
    public function withQuery(string $query): UriInterface
    {
        $clone = clone $this;
        $clone->setQuery($query);
        return $clone;
    }

    protected function setQuery(string $query): self
    {
        $this->queryPairs = [];
        foreach (explode('&', $query) as $pair) {
            if ($pair === '') {
                continue;
            }
            $parts = explode('=', $pair, 2);
            $this->queryPairs[] = [$parts[0], $parts[1] ?? null];
        }
        parse_str($query, $this->query);
        return $this;
    }

    #[Override]
    public function getQuery(): string
    {
        $query = [];
        foreach ($this->queryPairs as [$key, $value]) {
            $query[] = $this->encodeQueryComponent($key)
                . ($value === null ? '' : '=' . $this->encodeQueryComponent($value));
        }

        return implode('&', $query);
    }

    /**
     * Normalize a raw query key or value: decode it, then re-encode it as RFC3986 requires.
     *
     * A literal "+" is kept as written. It is a valid query character (RFC3986 sub-delim) and
     * form encoding reads it as a space, so turning it into "%2B" would change the value.
     */
    private function encodeQueryComponent(string $component): string
    {
        return implode('+', array_map(
            fn(string $segment): string => rawurlencode(rawurldecode($segment)),
            explode('+', $component)
        ));
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $isEncoded
     * @return $this
     */
    #[Override]
    public function withQueryKeyValue(string $key, string $value, bool $isEncoded = false): self
    {
        $clone = clone $this;
        $clone->setQueryPair(
            rawurlencode($key),
            rawurlencode($isEncoded ? rawurldecode($value) : $value)
        );
        return $clone;
    }

    /**
     * Replace the first occurrence of $rawKey (dropping any repeated ones) or append it.
     */
    private function setQueryPair(string $rawKey, string $rawValue): void
    {
        $pairs = [];
        $found = false;
        foreach ($this->queryPairs as $pair) {
            if ($this->encodeQueryComponent($pair[0]) !== $rawKey) {
                $pairs[] = $pair;
                continue;
            }
            if (!$found) {
                $pairs[] = [$rawKey, $rawValue];
                $found = true;
            }
        }
        if (!$found) {
            $pairs[] = [$rawKey, $rawValue];
        }

        $this->queryPairs = $pairs;
        parse_str($this->getQuery(), $this->query);
    }

    /**
     * Not from UriInterface
     *
     * The value of $key, or of its last occurrence if the key is repeated. Use getQueryParts()
     * to read every value.
     *
     * If the key is not in the query, it is looked up once more in the parse_str() index, so a
     * PHP-mangled name such as "group_id" still resolves "group.id". That fallback is
     * deprecated and is removed in 8.0.
     *
     * @param string $key
     * @return ?string
     */
    #[Override]
    public function getQueryPart(string $key): ?string
    {
        $values = $this->getQueryParts($key);
        if (count($values) > 0) {
            return end($values);
        }

        /** @deprecated Fallback on the parse_str() mangled key name; removed in 8.0. */
        $legacy = $this->query[$key] ?? null;
        return is_string($legacy) ? $legacy : null;
    }

    /**
     * Not from UriInterface
     *
     * Every value for $key, in the order they appear in the query. Unlike getQueryPart(),
     * this never falls back on the parse_str() mangled key names.
     *
     * @param string $key
     * @return list<string>
     */
    #[Override]
    public function getQueryParts(string $key): array
    {
        $values = [];
        foreach ($this->queryPairs as [$rawKey, $rawValue]) {
            if (rawurldecode($rawKey) === $key) {
                $values[] = $rawValue === null ? '' : rawurldecode($rawValue);
            }
        }

        return $values;
    }

    /**
     * As with getQueryPart(), a key that is not in the query falls back on the parse_str()
     * index. That fallback is deprecated and is removed in 8.0.
     */
    #[Override]
    public function hasQueryKey(string $key): bool
    {
        foreach ($this->queryPairs as [$rawKey]) {
            if (rawurldecode($rawKey) === $key) {
                return true;
            }
        }

        return isset($this->query[$key]);
    }

    /**
     * @param array $array
     * @param string $key
     * @param null|string $default
     * @return string|null
     */
    private function getFromArray(array $array, string $key, string|null $default): ?string
    {
        return $array[$key] ?? $default;
    }

    private function getIntFromArray(array $array, string $key): ?int
    {
        return empty($array[$key]) ? null : intval($array[$key]);
    }

    #[Override]
    public function getFragment(): string
    {
        return $this->fragment;
    }

    #[Override]
    public function withFragment(string $fragment): UriInterface
    {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    #[Override]
    public function getAuthority(): string
    {
        return
            $this->concatSuffix($this->getUserInfo(), "@")
            . $this->getHost()
            . $this->concatPrefix(':', strval($this->getPort()));
    }

    public function __toString(): string
    {
        $authority = $this->getAuthority();
        $path = $this->path;

        // PSR-7: a rootless path is prefixed by "/" when there is an authority.
        // Unlike PSR-7, a URI without a scheme keeps its authority without "//", so "h:9092" round-trips,
        // and "//" always follows a scheme, even without a host (sqlite:///tmp/db).
        if ($authority !== '' && $path !== '' && $path[0] !== '/') {
            $path = '/' . $path;
        }

        return
            $this->concatSuffix($this->getScheme(), '://')
            . $authority
            . $path
            . $this->concatPrefix('?', $this->getQuery())
            . $this->concatPrefix('#', $this->getFragment());
    }

    private function concatSuffix(string $str, string $suffix): string
    {
        if ($str !== '') {
            $str = $str . $suffix;
        }
        return $str;
    }

    private function concatPrefix(string $prefix, ?string $str): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return $prefix . $str;
    }

    /**
     * @param string|null $uri
     * @throws InvalidArgumentException when the URI starts with ":" (an empty scheme)
     */
    public function __construct(?string $uri = null)
    {
        if ($uri === null || $uri === '') {
            return;
        }
        if ($uri[0] === ':') {
            throw new InvalidArgumentException("Invalid URI, the scheme is empty: $uri");
        }

        $pattern = "/^"
            . "(?:(?P<scheme>\w+):\/\/)?"
            . "(?:(?P<user>\S*?):(?P<pass>\S*)@)?"
            . "(?:(?P<user2>\S+)@)?"
            . "(?P<host>\[[0-9A-Fa-f:.]+(?:%25[\w\-.~]+)?\]|(?![A-Za-z]:(?!\d))[\w\-]+(?:\.[\w\-]+)*)?"
            . "(?::(?P<port>\d+))?"
            . "(?P<path>([A-Za-z]:)?[^?#]+)?"
            . "(?:\?(?P<query>[^#]+))?"
            . "(?:#(?P<fragment>.*))?"
            . "$/";
        // Unmatched groups are null, so a URI without a userinfo (null) is told apart from
        // an empty user or password (":pw@", "user:@").
        preg_match($pattern, $uri, $parsed, PREG_UNMATCHED_AS_NULL);

        $user = $this->getFromArray($parsed, 'user', null)
            ?? $this->getFromArray($parsed, 'user2', null);
        $pass = $this->getFromArray($parsed, 'pass', null);

        $this->scheme = strtolower($this->getFromArray($parsed, 'scheme', '') ?? '');
        $this->host = strtolower($this->getFromArray($parsed, 'host', '') ?? '');
        $this->port = $this->getIntFromArray($parsed, 'port');
        $this->username = $user === null ? null : rawurldecode($user);
        $this->password = $pass === null ? null : rawurldecode($pass);
        $this->path = $this->getFromArray($parsed, 'path', '') ?? '';
        $this->setQuery($this->getFromArray($parsed, 'query', '') ?? '');
        $this->fragment = $this->getFromArray($parsed, 'fragment', '') ?? '';
    }

    public static function getInstance(string|UriInterface|null $uri = null): static
    {
        if ($uri instanceof UriInterface) {
            return new static((string)$uri);
        }

        return new static($uri);
    }
}
