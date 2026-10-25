<?php

namespace ByJG\Util;

use Override;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-17 factory for Uri, for code that receives a UriFactoryInterface instead of
 * creating the Uri itself.
 */
class UriFactory implements UriFactoryInterface
{
    #[Override]
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
