<?php


namespace ByJG\Util;


use Psr\Http\Message\UriInterface;

interface CustomUriInterface extends UriInterface
{
    public function getUsername();
    public function getPassword();
    public function getQueryPart($key);
    public function withQueryKeyValue($key, $value, $isEncoded = true);
    public function hasQueryKey($key): bool;
}
