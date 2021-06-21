<?php


namespace ByJG\Util;


interface CustomUriInterface
{
    public function getUsername();
    public function getPassword();
    public function getQueryPart($key);
    public function withQueryKeyValue($key, $value, $isEncoded = true);
}
