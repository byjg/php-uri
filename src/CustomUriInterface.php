<?php


namespace ByJG\Util;


interface CustomUriInterface
{
    public function getUsername(): ?string;
    public function getPassword(): ?string;
    public function getQueryPart(string $key): ?string;
    public function withQueryKeyValue(string $key, string $value, bool $isEncoded = true): self;
    public function hasQueryKey(string $key): bool;
}
