<?php


namespace ByJG\Util;


use Psr\Http\Message\UriInterface;

interface CustomUriInterface extends UriInterface
{
    public function getUsername(): ?string;
    public function getPassword(): ?string;
    public function getQueryPart(string $key): ?string;
    public function withQueryKeyValue(string $key, string $value, bool $isEncoded = false): self;
    public function hasQueryKey(string $key): bool;
}
