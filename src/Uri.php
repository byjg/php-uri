<?php

namespace ByJG\Util;

use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 */
class Uri implements UriInterface, CustomUriInterface
{
    private string $host = '';
    private string $fragment = '';
    private string $path = '';
    private string $scheme = '';
    private ?int $port = null;
    private array $query = [];

    public function withScheme(string $scheme): UriInterface
    {
        $clone = clone $this;
        $clone->scheme = strtolower($scheme);
        return $clone;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    private ?string $username = null;
    private ?string $password = null;

    public function withUserInfo(string $user, string $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->username = $user;
        $clone->password = $password;
        return $clone;
    }

    public function getUserInfo(): string
    {
        return $this->username
            . (!empty($this->password) ? ':' . rawurlencode($this->password) : '' );
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function withHost(string $host): UriInterface
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param int|null $port
     * @return $this
     */
    public function withPort(?int $port): UriInterface
    {
        $clone = clone $this;
        $clone->port = is_numeric($port) ? intval($port) : null;
        return $clone;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function withPath(string $path): UriInterface
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function withQuery(string $query): UriInterface
    {
        $clone = clone $this;
        $clone->setQuery($query);
        return $clone;
    }

    protected function setQuery($query): self
    {
        parse_str($query, $this->query);
        return $this;
    }


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
    public function withQueryKeyValue(string $key, string $value, bool $isEncoded = false): self
    {
        $clone = clone $this;
        $clone->query[$key] = ($isEncoded ? rawurldecode($value) : $value);
        return $clone;
    }

    /**
     * Not from UriInterface
     *
     * @param $key
     * @return ?string
     */
    public function getQueryPart($key): ?string
    {
        return $this->getFromArray($this->query, $key, null);
    }

    private function getFromArray($array, $key, $default): ?string
    {
        return $array[$key] ?? $default;
    }

    private function getIntFromArray($array, $key): ?int
    {
        return empty($array[$key]) ? null : intval($array[$key]);
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    public function getAuthority(): string
    {
        return
            $this->concatSuffix($this->getUserInfo(), "@")
            . $this->getHost()
            . $this->concatPrefix(':', $this->getPort());
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

    private function concatSuffix($str, $suffix)
    {
        if (!empty($str)) {
            $str = $str . $suffix;
        }
        return $str;
    }

    private function concatPrefix($prefix, $str)
    {
        if (!empty($str)) {
            $str = $prefix . $str;
        }
        return $str;
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
            . "(?:(?P<host>(?![A-Za-z]:)[\w\d\-]+(?:\.[\w\d\-]+)*))?"
            . "(?::(?P<port>[\d]+))?"
            . "(?P<path>([A-Za-z]:)?[^?#]+)?"
            . "(?:\?(?P<query>[^#]+))?"
            . "(?:#(?P<fragment>.*))?"
            . "$/";
        preg_match($pattern, $uri, $parsed);

        $user = $this->getFromArray($parsed, 'user', null);
        if (empty($user)) {
            $user = $this->getFromArray($parsed, 'user2', null);
        }

        $this->scheme = $this->getFromArray($parsed, 'scheme', "");
        $this->host = $this->getFromArray($parsed, 'host', "");
        $this->port = $this->getIntFromArray($parsed, 'port');
        $this->username = $user;
        $this->password = rawurldecode($this->getFromArray($parsed, 'pass', ""));
        $this->path = preg_replace('~^//~', '', $this->getFromArray($parsed, 'path', ""));
        $this->path = empty($this->path) ? "" : $this->path;
        $this->setQuery($this->getFromArray($parsed, 'query', ""));
        $this->fragment = $this->getFromArray($parsed, 'fragment', "");
    }

    public static function getInstanceFromString($uriString = null): Uri
    {
        return new Uri($uriString);
    }

    public static function getInstanceFromUri(UriInterface $uri): Uri
    {
        return self::getInstanceFromString((string)$uri);
    }
}
