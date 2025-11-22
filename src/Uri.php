<?php

namespace ByJG\Util;

use Override;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 */
class Uri implements CustomUriInterface
{
    private string $host = '';
    private string $fragment = '';
    private string $path = '';
    private string $scheme = '';
    private ?int $port = null;
    private array $query = [];

    #[Override]
    public function withScheme(string $scheme): UriInterface
    {
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

    #[Override]
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->username = $user;
        $clone->password = $password;
        return $clone;
    }

    #[Override]
    public function getUserInfo(): string
    {
        return ($this->username ?? "")
            . (!empty($this->password) ? ':' . rawurlencode($this->password) : '' );
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
        $clone->host = $host;
        return $clone;
    }

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
        return $this->port;
    }

    #[Override]
    public function withPath(string $path): UriInterface
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    #[Override]
    public function getPath(): string
    {
        return $this->path;
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
        parse_str($query, $this->query);
        return $this;
    }

    #[Override]
    public function getQuery(): string
    {
        return http_build_query($this->query, "", "&", PHP_QUERY_RFC3986);
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
        $clone->query[$key] = ($isEncoded ? rawurldecode($value) : $value);
        return $clone;
    }

    /**
     * Not from UriInterface
     *
     * @param string $key
     * @return ?string
     */
    #[Override]
    public function getQueryPart(string $key): ?string
    {
        return $this->getFromArray($this->query, $key, null);
    }

    #[Override]
    public function hasQueryKey(string $key): bool
    {
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
        return
            $this->concatSuffix($this->getScheme(), '://')
            . $this->getAuthority()
            . $this->getPath()
            . $this->concatPrefix('?', $this->getQuery())
            . $this->concatPrefix('#', $this->getFragment());
    }

    private function concatSuffix(string $str, string $suffix): string
    {
        if (!empty($str)) {
            $str = $str . $suffix;
        }
        return $str;
    }

    private function concatPrefix(string $prefix, ?string $str): string
    {
        if (!empty($str)) {
            $str = $prefix . $str;
        }
        return $str ?? "";
    }

    /**
     * @param string|null $uri
     */
    public function __construct(?string $uri = null)
    {
        if (empty($uri)) {
            return;
        }

        $pattern = "/^"
            . "(?:(?P<scheme>\w+):\/\/)?"
            . "(?:(?P<user>\S+?):(?P<pass>\S+)@)?"
            . "(?:(?P<user2>\S+)@)?"
            . "(?P<host>(?![A-Za-z]:)[\w\-]+(?:\.[\w\-]+)*)?"
            . "(?::(?P<port>\d+))?"
            . "(?P<path>([A-Za-z]:)?[^?#]+)?"
            . "(?:\?(?P<query>[^#]+))?"
            . "(?:#(?P<fragment>.*))?"
            . "$/";
        preg_match($pattern, $uri, $parsed);

        $user = $this->getFromArray($parsed, 'user', null);
        if (empty($user)) {
            $user = $this->getFromArray($parsed, 'user2', null);
        }

        $this->scheme = $this->getFromArray($parsed, 'scheme', '') ?? '';
        $this->host = $this->getFromArray($parsed, 'host', '') ?? '';
        $this->port = $this->getIntFromArray($parsed, 'port');
        $this->username = $user;
        $this->password = rawurldecode($this->getFromArray($parsed, 'pass', '') ?? '');
        $this->path = preg_replace('~^//~', '', $this->getFromArray($parsed, 'path', '') ?? '') ?? '';
        $this->setQuery($this->getFromArray($parsed, 'query', '') ?? '');
        $this->fragment = $this->getFromArray($parsed, 'fragment', '') ?? '';
    }

    /**
     * @param null|string $uriString
     * @deprecated use getInstance
     * @return UriInterface
     */
    public static function getInstanceFromString(string|null $uriString = null): UriInterface
    {
        return self::getInstance($uriString);
    }

    /**
     * @param UriInterface $uri
     * @deprecated use getInstance
     * @return UriInterface
     */
    public static function getInstanceFromUri(UriInterface $uri): UriInterface
    {
        return self::getInstance($uri);
    }

    public static function getInstance(string|UriInterface|null $uri = null): UriInterface
    {
        if ($uri instanceof UriInterface) {
            return new Uri((string)$uri);
        }

        return new Uri($uri);
    }
}
