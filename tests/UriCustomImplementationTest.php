<?php

namespace Test;

use ByJG\Util\Uri;
use PHPUnit\Framework\TestCase;

class UriCustomImplementationTest extends TestCase
{

    /**
     * @param $uriStr
     * @param null $assertFields
     */
    public function testCustomImplementation()
    {
        $uri = new Uri("https://user:pass@host:1234/path?query=1&test=2#fragment");
        $this->assertEquals("user", $uri->getUsername());
        $this->assertEquals("pass", $uri->getPassword());
        $this->assertEquals("1", $uri->getQueryPart("query"));
        $this->assertEquals("2", $uri->getQueryPart("test"));
        $this->assertEmpty($uri->getQueryPart("other"));
        $this->assertTrue($uri->hasQueryKey("query"));
        $this->assertFalse($uri->hasQueryKey("other"));

        $uri = $uri->withQueryKeyValue("other", "value");
        $this->assertEquals("1", $uri->getQueryPart("query"));
        $this->assertEquals("2", $uri->getQueryPart("test"));
        $this->assertEquals("value", $uri->getQueryPart("other"));
    }
}
