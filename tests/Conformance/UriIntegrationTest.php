<?php

namespace Tests\Conformance;

use ByJG\Util\UriFactory;
use Http\Psr7Test\UriIntegrationTest as Psr7UriIntegrationTest;
use Psr\Http\Message\UriInterface;

/**
 * php-http's PSR-7 conformance tests for Uri, the suite other PSR-7 implementations run.
 * Run it with: vendor/bin/phpunit --testsuite Conformance
 */
class UriIntegrationTest extends Psr7UriIntegrationTest
{
    #[\Override]
    public function createUri($uri): UriInterface
    {
        return (new UriFactory())->createUri($uri);
    }
}
