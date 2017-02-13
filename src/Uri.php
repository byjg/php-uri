<?php

namespace ByJG\Util;

use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 */
class Uri implements UriInterface
{


    private $scheme;

    public function withScheme($value)
    {
        $this->scheme = $value;
        return $this;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    private $username;
    private $password;

    public function withUserInfo($user, $password = null)
    {
        $this->username = $user;
        $this->password = $password;
        return $this;
    }

    public function getUserInfo()
    {
        return $this->username
            . (!empty($this->password) ? ':' . $this->password : '' );
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    private $host;

    public function withHost($value)
    {
        $this->host = $value;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    private $port;

    /**
     * @param int|string|null $value
     * @return $this
     */
    public function withPort($value)
    {
        $this->port = $value;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    private $path;

    public function withPath($value)
    {
        $this->path = $value;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    private $query = [];

    public function withQuery($query)
    {
        parse_str($query, $this->query);
        return $this;
    }


    public function getQuery()
    {
        return http_build_query($this->query);
    }

    /**
     * @param string $key
     * @param string|array $value
     * @param bool $encode
     * @return $this
     */
    public function withQueryKeyValue($key, $value, $encode = true)
    {
        $this->query[$key] = ($encode ? urlencode($value) : $value);
        return $this;
    }

    /**
     * Not from UriInterface
     *
     * @param $key
     * @return string
     */
    public function getQueryPart($key)
    {
        return $this->getFromArray($this->query, $key);
    }

    private function getFromArray($array, $key)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return null;
    }

    private $fragment;

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function getAuthority()
    {
        return
            $this->concatSuffix($this->getUserInfo(), "@")
            . $this->getHost()
            . $this->concatPrefix(':', $this->getPort());
    }

    public function __toString()
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
     * @param string $uri
     */
    public function __construct($uri = null)
    {
        if (empty($uri)) {
            return;
        }

        $parsed = parse_url($uri);

        $this->withScheme($this->getFromArray($parsed, 'scheme'));
        $this->withHost($this->getFromArray($parsed, 'host'));
        $this->withPort($this->getFromArray($parsed, 'port'));
        $this->withUserInfo($this->getFromArray($parsed, 'user'), $this->getFromArray($parsed, 'pass'));
        $this->withPath(preg_replace('~^//~', '', $this->getFromArray($parsed, 'path')));
        $this->withQuery($this->getFromArray($parsed, 'query'));
        $this->withFragment($this->getFromArray($parsed, 'fragment'));

        $final = $this->__toString();
        // Try recover if parse_url fails :(
        if (empty($final)) {
            $patScheme = "(?P<scheme>[\w\.]+)\:\/\/";
            $patCredentials = "(?:((?P<username>\S+):(?P<password>\S+)|(?P<username2>\S+))@)?";
            $patHost = "((?P<host>[\w\-\.,_]+)(?::(?P<port>\d+))?)?";
            $patPath = "(?P<path>([\/\w\-\.]+))?";
            $patQuery = "(?:\?(?P<query>(?:[\w\-\.]+=[\w\-%\.\/]+&?)*))?";

            $matches = [];
            if (preg_match("~$patScheme$patCredentials$patHost$patPath$patQuery~", $uri, $matches)) {
                $this->withScheme($matches['scheme']);
                $this->withUserInfo($matches['username'], $matches['password']);
                $this->withHost($matches['host']);
                $this->withPort($matches['port']);
                $this->withPath($matches['path']);
                $this->withQuery(isset($matches['query']) ? $matches['query'] : null);
            }
        }
    }
}
