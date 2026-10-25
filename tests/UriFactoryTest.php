<?php

namespace Tests;

use ByJG\Util\Uri;
use ByJG\Util\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriFactoryInterface;

class UriFactoryTest extends TestCase
{
    public function testIsAPsr17UriFactory(): void
    {
        $this->assertInstanceOf(UriFactoryInterface::class, new UriFactory());
    }

    public function testCreatesTheSameUriAsTheConstructor(): void
    {
        $string = 'mysql://user:p%40ss@db:3306/shop?charset=utf8#frag';

        $uri = (new UriFactory())->createUri($string);

        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertSame($string, (string)$uri);
        $this->assertSame('p@ss', $uri->getPassword());
        $this->assertSame(3306, $uri->getPort());
    }

    public function testCreatesAnEmptyUriByDefault(): void
    {
        $this->assertSame('', (string)(new UriFactory())->createUri());
    }
}
