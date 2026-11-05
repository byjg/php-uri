<?php

namespace Tests\Conformance;

use ByJG\Util\UriFactory;
use Http\Psr7Test\UriIntegrationTest as Psr7UriIntegrationTest;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\UriInterface;

/**
 * php-http's PSR-7 conformance tests for Uri, the suite other PSR-7 implementations run.
 *
 * The cases where Uri deviates from PSR-7 on purpose are skipped with the reason, so the run lists them.
 */
class UriIntegrationTest extends Psr7UriIntegrationTest
{
    /**
     * The data sets of testPath() that Uri deviates from, by their index in getPaths()
     */
    private const array PATH_DEVIATIONS = [
        2 => 'A URI without a scheme is read as host[:port][/path], as a DSN ("localhost:6379"), so "foo/bar"'
            . ' has the host "foo"; PSR-7 reads it as a relative path',
        3 => 'getPath() returns the path as given, not percent-encoded: the drivers read it as a name (a file,'
            . ' a database, a bucket); PSR-7 returns "/foo bar" as "/foo%20bar"',
    ];

    #[\Override]
    public function createUri($uri): UriInterface
    {
        return (new UriFactory())->createUri($uri);
    }

    #[\Override]
    #[DataProvider('getPaths')]
    public function testPath(UriInterface $uri, string $expected)
    {
        $reason = self::PATH_DEVIATIONS[$this->dataName()] ?? null;
        if ($reason !== null) {
            $this->markTestSkipped($reason);
        }
        parent::testPath($uri, $expected);
    }

    #[\Override]
    public function testAlreadyEncodedUserInfo(): void
    {
        $this->markTestSkipped(
            'withUserInfo() takes the user and the password as they are, not encoded: a password with "%41"'
            . ' keeps it; PSR-7 implementations read "%XX" as already encoded'
        );
    }
}
