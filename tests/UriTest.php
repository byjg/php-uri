<?php

namespace Test;

use ByJG\Util\CustomUriInterface;
use ByJG\Util\Uri;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class UriTest extends TestCase
{

    public static function uriProvider(): array
    {
        return [
            [ // #0
                'https://username:password@hostname/path?arg=value#anchor',
                [
                    'Scheme' => 'https',
                    'Username' => 'username',
                    'Password' => 'password',
                    'Userinfo' => 'username:password',
                    'Host' => 'hostname',
                    'Port' => null,
                    'Path' => '/path',
                    'Query' => 'arg=value',
                    'Fragment' => 'anchor',
                    'Authority' => 'username:password@hostname'
                ]
            ],
            [ // #1
                'https://username:password@hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'https',
                    'Username' => 'username',
                    'Password' => 'password',
                    'Userinfo' => 'username:password',
                    'Host' => 'hostname',
                    'Port' => null,
                    'Path' => '/path/path2',
                    'Query' => 'arg=value&arg2=value2',
                    'Fragment' => 'anchor',
                    'Authority' => 'username:password@hostname'
                ]
            ],
            [ // #2
                'https://hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'https',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'hostname',
                    'Port' => null,
                    'Path' => '/path/path2',
                    'Query' => 'arg=value&arg2=value2',
                    'Fragment' => 'anchor',
                    'Authority' => 'hostname'
                ]
            ],
            [ // #3
                'https://username@hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'https',
                    'Username' => 'username',
                    'Password' => null,
                    'Userinfo' => 'username',
                    'Host' => 'hostname',
                    'Port' => null,
                    'Path' => '/path/path2',
                    'Query' => 'arg=value&arg2=value2',
                    'Fragment' => 'anchor',
                    'Authority' => 'username@hostname'
                ]
            ],
            [ // #4
                'https://email@host.com.br:password@hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'https',
                    'Username' => 'email@host.com.br',
                    'Password' => 'password',
                    'Userinfo' => 'email@host.com.br:password',
                    'Host' => 'hostname',
                    'Port' => null,
                    'Path' => '/path/path2',
                    'Query' => 'arg=value&arg2=value2',
                    'Fragment' => 'anchor',
                    'Authority' => 'email@host.com.br:password@hostname'
                ]
            ],
            [ // #5
                'file:///home/user/file.txt',
                [
                    'Scheme' => 'file',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => '/home/user/file.txt',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #6
                'sqlite:///home/user/file.txt',
                [
                    'Scheme' => 'sqlite',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => '/home/user/file.txt',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #7
                'https://hostname.com:443',
                [
                    'Scheme' => 'https',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Port' => 443,
                    'Host' => 'hostname.com',
                    'Path' => '',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'hostname.com:443'
                ]
            ],
            [ // #8
                'https://hostname.com/#anchor',
                [
                    'Scheme' => 'https',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'hostname.com',
                    'Port' => null,
                    'Path' => '/',
                    'Query' => null,
                    'Fragment' => 'anchor',
                    'Authority' => 'hostname.com'
                ]
            ],
            [ // #9
                'mysql://root:password@host-10.com:3306/database?extra_param=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => 'password',
                    'Userinfo' => 'root:password',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extra_param=10',
                    'Fragment' => '',
                    'Authority' => 'root:password@host-10.com:3306'
                ]
            ],
            [ // #10
                'mysql://ro@11!%&*(ot:pass@(*&!$$word@host-10.com:3306/database?extra_param=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'ro@11!%&*(ot',
                    'Password' => 'pass@(*&!$$word',
                    'Userinfo' => 'ro@11!%&*(ot:pass%40%28%2A%26%21%24%24word',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extra_param=10',
                    'Fragment' => '',
                    'Authority' => 'ro@11!%&*(ot:pass%40%28%2A%26%21%24%24word@host-10.com:3306',
                    'ToString' => 'mysql://ro@11!%&*(ot:pass%40%28%2A%26%21%24%24word@host-10.com:3306/database?extra_param=10'
                ]
            ],
            [ // #11
                'mysql://root@host-10.com:3306/database?extra_param=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => null,
                    'Userinfo' => 'root',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extra_param=10',
                    'Fragment' => '',
                    'Authority' => 'root@host-10.com:3306'
                ]
            ],
            [ // #12
                'mysql://root@host-10.com:3306/database?extra_param=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => null,
                    'Userinfo' => 'root',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extra_param=10',
                    'Fragment' => '',
                    'Authority' => 'root@host-10.com:3306'
                ]
            ],
            [ // #13
                'mysql://host-10.com:3306/database?extra_param=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extra_param=10',
                    'Fragment' => '',
                    'Authority' => 'host-10.com:3306'
                ]
            ],
            [ // #14
                'mysql://host-10.com/database',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => null,
                    'Path' => '/database',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'host-10.com'
                ]
            ],
            [ // #15
                'mysql://host-10.com:3306/database',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'host-10.com:3306'
                ]
            ],
            [ // #16
                'mysql://host-10.com:3306?extra_param=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '',
                    'Query' => 'extra_param=10',
                    'Fragment' => '',
                    'Authority' => 'host-10.com:3306'
                ]
            ],
            [ // #17
                'mysql://host-10.com:3306?extra_param=10&other=20',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '',
                    'Query' => 'extra_param=10&other=20',
                    'Fragment' => '',
                    'Authority' => 'host-10.com:3306'
                ]
            ],
            [ // #18
                'smtp://us#$%er:pa!*&$ss@host.com.br:45',
                [
                    'Scheme' => 'smtp',
                    'Username' => 'us#$%er',
                    'Password' => 'pa!*&$ss',
                    'Userinfo' => 'us#$%er:pa%21%2A%26%24ss',
                    'Host' => 'host.com.br',
                    'Port' => 45,
                    'Path' => '',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'us#$%er:pa%21%2A%26%24ss@host.com.br:45',
                    'ToString' => 'smtp://us#$%er:pa%21%2A%26%24ss@host.com.br:45',
                ]
            ],
            [ // #19
                'smtp://us:er:pass@host.com.br:45',
                [
                    'Scheme' => 'smtp',
                    'Username' => 'us',
                    'Password' => 'er:pass',
                    'Userinfo' => 'us:er%3Apass',
                    'Host' => 'host.com.br',
                    'Port' => 45,
                    'Path' => '',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'us:er%3Apass@host.com.br:45',
                    'ToString' => 'smtp://us:er%3Apass@host.com.br:45',
                ]
            ],
            [ // #20
                '/some/relative/path',
                [
                    'Scheme' => null,
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => '/some/relative/path',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #21  -> https://tools.ietf.org/html/rfc3986#section-3.2.2
                'urn://user:pass@:123/path',
                [
                    'Scheme' => 'urn',
                    'Username' => 'user',
                    'Password' => 'pass',
                    'Userinfo' => 'user:pass',
                    'Host' => null,
                    'Port' => 123,
                    'Path' => '/path',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'user:pass@:123'
                ]
            ],
            [ // #22
                'sqlite://C:\\Windows\\Path\\file.db',
                [
                    'Scheme' => 'sqlite',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => 'C:\\Windows\\Path\\file.db',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #23
                'C:\\Windows\\Path\\file.db',
                [
                    'Scheme' => null,
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => 'C:\\Windows\\Path\\file.db',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #24
                'sqlite://C:/Windows/Path/file.db',
                [
                    'Scheme' => 'sqlite',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => 'C:/Windows/Path/file.db',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #25
                'C:/Windows/Path/file.db',
                [
                    'Scheme' => null,
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => null,
                    'Port' => null,
                    'Path' => 'C:/Windows/Path/file.db',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => null
                ]
            ],
            [ // #26
                'mysql://root@host-10.com:3306/database?ca=%2Fpath%2Fto%2Fca&ssl=%2Fpath%2Fto%2Fssl',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => null,
                    'Userinfo' => 'root',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'ca=%2Fpath%2Fto%2Fca&ssl=%2Fpath%2Fto%2Fssl',
                    'Fragment' => '',
                    'Authority' => 'root@host-10.com:3306'
                ]
            ],
            [ // #27
                'https://user:O=+9zLZ}%{z+:tC@host/path',
                [
                    'Scheme' => 'https',
                    'Username' => "user",
                    'Password' => "O=+9zLZ}%{z+:tC",
                    'Userinfo' => "user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC",
                    'Host' => "host",
                    'Port' => null,
                    'Path' => '/path',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => "user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC@host",
                    'ToString' => 'https://user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC@host/path',
                ]
            ],
            [ // #28
                'https://host/path?key=value 1&key2=á%1$@a#fra%!',
                [
                    'Scheme' => 'https',
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => "host",
                    'Port' => null,
                    'Path' => '/path',
                    'Query' => 'key=value%201&key2=%C3%A1%251%24%40a',
                    'Fragment' => 'fra%!',
                    'Authority' => "host",
                    'ToString' => 'https://host/path?key=value%201&key2=%C3%A1%251%24%40a#fra%!',
                ]
            ],
            [ // #29
                'https://example.com/path/to?q=foo bar&q2=foo%20bar&q3=abc%3D%41#section-42',
                [
                    'Scheme' => 'https',
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => "example.com",
                    'Port' => null,
                    'Path' => '/path/to',
                    'Query' => 'q=foo%20bar&q2=foo%20bar&q3=abc%3DA',
                    'Fragment' => 'section-42',
                    'Authority' => "example.com",
                    'ToString' => 'https://example.com/path/to?q=foo%20bar&q2=foo%20bar&q3=abc%3DA#section-42',
                ]
            ],
            [ // #30
                'kafka://h:9092',
                [
                    'Scheme' => 'kafka',
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => 'h',
                    'Port' => 9092,
                    'Path' => '',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'h:9092',
                    'ToString' => 'kafka://h:9092',
                ]
            ],
            [ // #31
                'kafka://a:9092/path',
                [
                    'Scheme' => 'kafka',
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => 'a',
                    'Port' => 9092,
                    'Path' => '/path',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'a:9092',
                    'ToString' => 'kafka://a:9092/path',
                ]
            ],
            [ // #32
                'mysql://user:pw@h:3306/db',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'user',
                    'Password' => 'pw',
                    'Userinfo' => 'user:pw',
                    'Host' => 'h',
                    'Port' => 3306,
                    'Path' => '/db',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'user:pw@h:3306',
                    'ToString' => 'mysql://user:pw@h:3306/db',
                ]
            ],
            [ // #33
                'h:9092',
                [
                    'Scheme' => "",
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => 'h',
                    'Port' => 9092,
                    'Path' => '',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => 'h:9092',
                    'ToString' => 'h:9092',
                ]
            ],
            [ // #34
                'kafka://[::1]:9092',
                [
                    'Scheme' => 'kafka',
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => '[::1]',
                    'Port' => 9092,
                    'Path' => '',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => '[::1]:9092',
                    'ToString' => 'kafka://[::1]:9092',
                ]
            ],
            [ // #35
                'https://user:pw@[2001:db8::1]:443/p?q=1#f',
                [
                    'Scheme' => 'https',
                    'Username' => 'user',
                    'Password' => 'pw',
                    'Userinfo' => 'user:pw',
                    'Host' => '[2001:db8::1]',
                    'Port' => 443,
                    'Path' => '/p',
                    'Query' => 'q=1',
                    'Fragment' => 'f',
                    'Authority' => 'user:pw@[2001:db8::1]:443',
                    'ToString' => 'https://user:pw@[2001:db8::1]:443/p?q=1#f',
                ]
            ],
            [ // #36
                'http://[::ffff:192.0.2.1]/p',
                [
                    'Scheme' => 'http',
                    'Username' => "",
                    'Password' => "",
                    'Userinfo' => "",
                    'Host' => '[::ffff:192.0.2.1]',
                    'Port' => null,
                    'Path' => '/p',
                    'Query' => null,
                    'Fragment' => '',
                    'Authority' => '[::ffff:192.0.2.1]',
                    'ToString' => 'http://[::ffff:192.0.2.1]/p',
                ]
            ],
        ];
    }

    #[DataProvider('uriProvider')]
    public function testParseScheme(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Scheme"], $uri->getScheme());
    }

    #[DataProvider('uriProvider')]
    public function testParseUsername(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Username"], $uri->getUsername());
    }

    #[DataProvider('uriProvider')]
    public function testParsePassword(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Password"], $uri->getPassword());
    }

    #[DataProvider('uriProvider')]
    public function testParseUserinfo(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Userinfo"], $uri->getUserinfo());
    }

    #[DataProvider('uriProvider')]
    public function testParseHost(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Host"], $uri->getHost());
    }

    #[DataProvider('uriProvider')]
    public function testParsePort(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Port"], $uri->getPort());
    }

    #[DataProvider('uriProvider')]
    public function testParsePath(string $uriStr, array $assertFields = [])
    {
        $uri = new Uri($uriStr);
        $this->assertSame($assertFields["Path"], $uri->getPath());
    }

    #[DataProvider('uriProvider')]
    public function testParseQuery(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Query"], $uri->getQuery());
    }

    #[DataProvider('uriProvider')]
    public function testParseFragment(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Fragment"], $uri->getFragment());
    }

    #[DataProvider('uriProvider')]
    public function testParseAuthority(string $uriStr, array $assertFields)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Authority"], $uri->getAuthority());
    }

    #[DataProvider('uriProvider')]
    public function testParseToString($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        if (isset($assertFields['ToString'])) {
            $uriStr = $assertFields['ToString'];
        }
        $this->assertEquals($uriStr, $uri->__toString());
    }

    public function testMountUrl1()
    {
        $this->assertEquals(
            'https://host.com:1234',
            Uri::getInstance()
                ->withScheme('https')
                ->withHost('host.com')
                ->withPort(1234)
        );
    }

    public function testMountUrl2()
    {
        $uri = new Uri('/some/relative/path');

        $this->assertEquals(
            'https://host.com/some/relative/path',
            $uri
                ->withScheme('https')
                ->withHost('host.com')
                ->__toString()
        );
    }

    public function testChangeParameters()
    {
        $uri = new Uri('https://foo-host.com/path?key=value&otherkey=othervalue#fragment');

        $this->assertEquals(
            'https://bar.net/otherpath?key=newvalue&otherkey=othervalue&newkey=value#fragment',
            $uri
                ->withHost('bar.net')
                ->withPath('/otherpath')
                ->withQueryKeyValue('key', 'newvalue')
                ->withQueryKeyValue('newkey', 'value')
        );
    }

    public function testFactory()
    {
        $uriString = 'https://user:pass@host/path?query=1#fragment';
        $uri = Uri::getInstance($uriString);
        $this->assertEquals($uriString, $uri->__toString());

        $uri2 = Uri::getInstance($uri);
        $this->assertEquals($uriString, $uri2->__toString());
    }

    public function testFactory2()
    {
        $uri = Uri::getInstance('https://example.com/path/to?q=foo%20bar#section-42')
            ->withUserInfo('user', "O=+9%20zLZ}%{z+:tC");

        $uri2 = Uri::getInstance('https://user:O=+9%2520zLZ}%{z+:tC@example.com/path/to?q=foo%20bar#section-42');

        $uri3 = Uri::getInstance($uri);

        $uri4 = Uri::getInstance((string)$uri);

        $this->assertSame((string)$uri, (string)$uri2);
        $this->assertSame((string)$uri2, (string)$uri3);
        $this->assertSame((string)$uri3, (string)$uri4);
    }

    public function testRFC3986()
    {
        $uri = Uri::getInstance("https://user:pa&@host");
        $this->assertEquals("https://user:pa%26@host", (string)$uri);

        $uri = Uri::getInstance("https://user:pa%26@host");
        $this->assertEquals("https://user:pa%26@host", (string)$uri);

        $uri = Uri::getInstance("https://host")
            ->withUserInfo("user", "pa%26");
        $this->assertEquals("https://user:pa%2526@host", (string)$uri);
    }

    public function testWithUrlEncoding()
    {
        $uri = Uri::getInstance('https://example.com/path/to?q=foo%20bar#section-42')
            ->withUserInfo('user', "O=+9zLZ}%{z+:tC");

        $this->assertEquals("q=foo%20bar", $uri->getQuery());
        /** @var CustomUriInterface $uri */
        $this->assertEquals("user", $uri->getUsername());
        $this->assertEquals("O=+9zLZ}%{z+:tC", $uri->getPassword());
        $this->assertEquals('user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC', $uri->getUserInfo());
    }

    /** @psalm-suppress UndefinedInterfaceMethod */
    public function testWithQueryValue()
    {
        $uri = Uri::getInstance("https://example.com")
            ->withQueryKeyValue("q", "abc")
            ->withQueryKeyValue("q1", "abc%3D%41")
            ->withQueryKeyValue("q2", "abc%3D%41", true);

        $this->assertEquals("q=abc&q1=abc%253D%2541&q2=abc%3DA", $uri->getQuery());
    }

    public function testImmutable()
    {
        $uri = new Uri();

        // With Host
        $uri1 = $uri->withHost("host");
        $this->assertEquals("", $uri->getHost());
        $this->assertEquals("host", $uri1->getHost());

        // With Path
        $uri2 = $uri->withPath("/path");
        $this->assertEquals("", $uri->getPath());
        $this->assertEquals("/path", $uri2->getPath());

        // With Port
        $uri3 = $uri->withPort(8080);
        $this->assertEquals("", $uri->getPort());
        $this->assertEquals(8080, $uri3->getPort());

        // With Scheme
        $uri4 = $uri->withScheme("https");
        $this->assertEquals("", $uri->getScheme());
        $this->assertEquals("https", $uri4->getScheme());

        // WithUserInfo
        $uri5 = $uri->withUserInfo("user", "pwd");
        $this->assertEquals("", $uri->getUserInfo());
        $this->assertEquals("user:pwd", $uri5->getUserInfo());

        // WithQuery
        $uri6 = $uri->withQuery("a=1&b=2");
        $this->assertEquals("", $uri->getQuery());
        $this->assertEquals("a=1&b=2", $uri6->getQuery());

        // WithFragment
        $uri7 = $uri->withFragment("fragment");
        $this->assertEquals("", $uri->getFragment());
        $this->assertEquals("fragment", $uri7->getFragment());
    }

    public function testGetFromArray()
    {
        $uri = new Uri("https://server/path?query=abc&arg#fragment");

        $this->assertEquals("abc", $uri->getQueryPart("query"));
        $this->assertSame("", $uri->getQueryPart("arg"));
        $this->assertNull($uri->getQueryPart("other"));
    }

    public function testHasQueryKey()
    {
        $uri = new Uri('https://example.com/path/to?q=foo%20bar#section-42');
        $this->assertTrue($uri->hasQueryKey('q'));
        $this->assertFalse($uri->hasQueryKey('q2'));
    }

    /**
     * The host group guards against reading a Windows drive letter as a host. The guard must
     * not reject a one-character host that carries a port -- "h:9092" is not "C:\\path".
     */
    public static function singleCharacterHostProvider(): array
    {
        return [
            ['kafka://h:9092', 'h', 9092, ''],
            ['kafka://a:9092/path', 'a', 9092, '/path'],
            ['https://x:8080', 'x', 8080, ''],
            ['mysql://user:pw@h:3306/db', 'h', 3306, '/db'],
            ['h:9092', 'h', 9092, ''],
            // a digit host already worked: the guard only ever looked at [A-Za-z]
            ['kafka://1:9092', '1', 9092, ''],
            // and the cases that were never broken must stay that way
            ['kafka://hh:9092', 'hh', 9092, ''],
            ['kafka://h', 'h', null, ''],
            ['kafka://localhost:9092', 'localhost', 9092, ''],
        ];
    }

    #[DataProvider('singleCharacterHostProvider')]
    public function testSingleCharacterHostWithPort(
        string $uriStr,
        string $host,
        ?int $port,
        string $path
    ) {
        $uri = new Uri($uriStr);

        $this->assertSame($host, $uri->getHost());
        $this->assertSame($port, $uri->getPort());
        $this->assertSame($path, $uri->getPath());
    }

    public static function windowsPathProvider(): array
    {
        return [
            ['sqlite://C:\\Windows\\Path\\file.db', 'C:\\Windows\\Path\\file.db'],
            ['sqlite://C:/Windows/Path/file.db', 'C:/Windows/Path/file.db'],
            ['C:\\Windows\\Path\\file.db', 'C:\\Windows\\Path\\file.db'],
            ['C:/Windows/Path/file.db', 'C:/Windows/Path/file.db'],
            // drive-relative forms: no path separator, so nothing marks them as a path
            // other than the absence of a port after the colon
            ['C:foo', 'C:foo'],
            ['C:', 'C:'],
        ];
    }

    /**
     * A drive letter must never be read as a host, whatever follows it -- unless what follows
     * is a port, which is the ambiguity documented in testDriveLetterWithDigitsIsReadAsAPort().
     */
    #[DataProvider('windowsPathProvider')]
    public function testWindowsPathIsNotReadAsAHost(string $uriStr, string $path)
    {
        $uri = new Uri($uriStr);

        $this->assertSame('', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame($path, $uri->getPath());
    }

    /**
     * Deliberate trade-off: "C:1" is a one-character host with port 1 and a drive-relative
     * path at the same time, and nothing in the string tells them apart. The host reading
     * wins, because a DSN is what this library parses.
     */
    public function testDriveLetterWithDigitsIsReadAsAPort()
    {
        $uri = new Uri('C:1');

        $this->assertSame('C', $uri->getHost());
        $this->assertSame(1, $uri->getPort());
        $this->assertSame('', $uri->getPath());
    }

    public static function ipv6Provider(): array
    {
        return [
            // Input, host, port, path, authority
            ['kafka://[::1]:9092', '[::1]', 9092, '', '[::1]:9092'],
            ['kafka://[::1]', '[::1]', null, '', '[::1]'],
            ['kafka://[2001:db8::1]:9092', '[2001:db8::1]', 9092, '', '[2001:db8::1]:9092'],
            // RFC 6874 zone identifier
            ['kafka://[fe80::1%25eth0]:9092', '[fe80::1%25eth0]', 9092, '', '[fe80::1%25eth0]:9092'],
            // IPv4-mapped
            ['http://[::ffff:192.0.2.1]/p', '[::ffff:192.0.2.1]', null, '/p', '[::ffff:192.0.2.1]'],
            ['https://user:pw@[2001:db8::1]:443/p', '[2001:db8::1]', 443, '/p', 'user:pw@[2001:db8::1]:443'],
        ];
    }

    /**
     * RFC3986 section 3.2.2: the authority host is IP-literal / IPv4address / reg-name, and
     * IP-literal keeps its brackets so the colons inside cannot be read as the port separator.
     */
    #[DataProvider('ipv6Provider')]
    public function testIpv6LiteralIsParsedAsHost(
        string $uriStr,
        string $host,
        ?int $port,
        string $path,
        string $authority
    ) {
        $uri = new Uri($uriStr);

        $this->assertSame($host, $uri->getHost());
        $this->assertSame($port, $uri->getPort());
        $this->assertSame($path, $uri->getPath());
        $this->assertSame($authority, $uri->getAuthority());

        // The failure this replaces was silent: the literal survived inside the path, so the
        // round-trip looked correct while every accessor was wrong. Assert both together.
        $this->assertSame($uriStr, (string)$uri);
    }

    public static function notAnIpLiteralProvider(): array
    {
        return [
            ['http://[]/p', '[]/p'],
            ['http://[zzz]/p', '[zzz]/p'],
            // IPvFuture (RFC3986 section 3.2.2) is deliberately not covered
            ['http://[v7.foo]/p', '[v7.foo]/p'],
            ['http://[::1', '[::1'],
            ['http://::1]/p', '::1]/p'],
        ];
    }

    /**
     * Anything that is not shaped like an IP-literal keeps the previous behaviour: no host,
     * and the text falls through to the path.
     */
    #[DataProvider('notAnIpLiteralProvider')]
    public function testUnbracketedOrUnrecognisedLiteralIsNotAHost(string $uriStr, string $path)
    {
        $uri = new Uri($uriStr);

        $this->assertSame('', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame($path, $uri->getPath());
    }

    /**
     * Deliberate scope: the IP-literal branch accepts the character set of an IPv6 address
     * rather than validating its structure, exactly as the reg-name branch does not validate
     * a hostname. Address validation belongs to whatever consumes the host.
     */
    public function testIpLiteralIsNotValidated()
    {
        $this->assertSame('[:::]', (new Uri('http://[:::]/p'))->getHost());
        $this->assertSame('[999.999.999.999]', (new Uri('http://[999.999.999.999]/p'))->getHost());
    }

    public static function queryProvider(): array
    {
        return [
            // Input query, expected getQuery()
            ['group.id=g1', 'group.id=g1'],                          // dot is unreserved (RFC3986 2.3)
            ['tag=x&tag=y', 'tag=x&tag=y'],                          // repeated keys are kept, in order
            ['a[]=1&a[]=2', 'a%5B%5D=1&a%5B%5D=2'],                  // brackets are not pchar, so encoded
            ['my%20key=v', 'my%20key=v'],                            // encoded key stays encoded
            ['flag', 'flag'],                                        // no "=" means no value
            ['a=1&b.c=2', 'a=1&b.c=2'],
            ['empty=', 'empty='],                                    // "=" with no value is kept
            ['session.timeout.ms=6000', 'session.timeout.ms=6000'],
            ['ca=%2Fpath%2Fto%2Fca', 'ca=%2Fpath%2Fto%2Fca'],        // reserved chars stay encoded
            ['q=foo bar', 'q=foo%20bar'],                            // invalid chars get encoded
            ['q=abc%3D%41', 'q=abc%3DA'],                            // unreserved chars get decoded
            ['q=Rio+de+Janeiro', 'q=Rio+de+Janeiro'],                // "+" is kept, never turned into %2B
            ['q=1%2B1', 'q=1%2B1'],                                  // an encoded "+" stays encoded
            ['q=a+b%2Bc', 'q=a+b%2Bc'],                              // both forms keep their own meaning
            ['a+b=1', 'a+b=1'],                                      // "+" in a key is kept too
        ];
    }

    #[DataProvider('queryProvider')]
    public function testQueryIsPreserved(string $query, string $expected)
    {
        $this->assertEquals($expected, (new Uri('https://host/path?' . $query))->getQuery());
    }

    #[DataProvider('queryProvider')]
    public function testQueryIsPreservedInToString(string $query, string $expected)
    {
        $this->assertEquals(
            'https://host/path?' . $expected,
            (string)(new Uri('https://host/path?' . $query))
        );
    }

    #[DataProvider('queryProvider')]
    public function testQueryNormalizationIsIdempotent(string $query, string $expected)
    {
        $uri = new Uri('https://host/path?' . $query);

        $this->assertEquals($expected, (new Uri((string)$uri))->getQuery());
    }

    /**
     * PSR-7: withQuery($query)->getQuery() must return the query it was given.
     */
    #[DataProvider('queryProvider')]
    public function testWithQueryReturnsTheSameQuery(string $query, string $expected)
    {
        $uri = (new Uri('https://host/path'))->withQuery($expected);

        $this->assertEquals($expected, $uri->getQuery());
    }

    public function testGetQueryPartWithDottedKey()
    {
        $uri = new Uri('https://host/path?session.timeout.ms=6000&auto.offset.reset=earliest');

        $this->assertEquals('6000', $uri->getQueryPart('session.timeout.ms'));
        $this->assertEquals('earliest', $uri->getQueryPart('auto.offset.reset'));
        $this->assertTrue($uri->hasQueryKey('session.timeout.ms'));
        $this->assertFalse($uri->hasQueryKey('session.timeout.us'));
    }

    public function testGetQueryPartWithEncodedKey()
    {
        $uri = new Uri('https://host/path?my%20key=a%20value');

        $this->assertEquals('a value', $uri->getQueryPart('my key'));
        $this->assertTrue($uri->hasQueryKey('my key'));
    }

    /**
     * The parse_str() index is kept as a fallback, so the mangled key still resolves.
     */
    public function testGetQueryPartWithLegacyMangledKey()
    {
        $uri = new Uri('https://host/path?group.id=g1');

        $this->assertEquals('g1', $uri->getQueryPart('group_id'));
    }

    /**
     * parse_str() turns "a[]" into an array; getQueryPart() must not choke on it.
     */
    public function testGetQueryPartWithBracketKeyDoesNotThrow()
    {
        $uri = new Uri('https://host/path?a[]=1&a[]=2');

        $this->assertNull($uri->getQueryPart('a'));
        $this->assertEquals('2', $uri->getQueryPart('a[]'));
    }

    public function testGetQueryPartWithRepeatedKeyReturnsTheLast()
    {
        $uri = new Uri('https://host/path?tag=x&tag=y');

        $this->assertEquals('y', $uri->getQueryPart('tag'));
    }

    public function testGetQueryPartsReturnsEveryValueInOrder()
    {
        $uri = new Uri('https://host/path?tag=x&page=1&tag=y&tag=z');

        $this->assertEquals(['x', 'y', 'z'], $uri->getQueryParts('tag'));
        $this->assertEquals(['1'], $uri->getQueryParts('page'));
    }

    public function testGetQueryPartsReturnsAnEmptyArrayWhenTheKeyIsAbsent()
    {
        $uri = new Uri('https://host/path?tag=x');

        $this->assertEquals([], $uri->getQueryParts('missing'));
    }

    public function testGetQueryPartsDecodesTheValues()
    {
        $uri = new Uri('https://host/path?my%20key=a%20value&my%20key=%2Fpath');

        $this->assertEquals(['a value', '/path'], $uri->getQueryParts('my key'));
    }

    public function testGetQueryPartsWithBracketKey()
    {
        $uri = new Uri('https://host/path?a[]=1&a[]=2');

        $this->assertEquals(['1', '2'], $uri->getQueryParts('a[]'));
    }

    public function testGetQueryPartsReturnsAnEmptyStringForAValuelessKey()
    {
        $uri = new Uri('https://host/path?flag');

        $this->assertSame([''], $uri->getQueryParts('flag'));
    }

    /**
     * The mangled-name fallback is deprecated, so the new method must not honour it.
     */
    public function testGetQueryPartsIgnoresTheLegacyMangledKey()
    {
        $uri = new Uri('https://host/path?group.id=g1');

        $this->assertEquals(['g1'], $uri->getQueryParts('group.id'));
        $this->assertEquals([], $uri->getQueryParts('group_id'));
    }

    public function testGetQueryPartsAfterWithQueryKeyValue()
    {
        $uri = (new Uri('https://host/path?tag=x&tag=y'))->withQueryKeyValue('tag', 'z');

        $this->assertEquals(['z'], $uri->getQueryParts('tag'));
    }

    public function testWithQueryKeyValueKeepsTheOtherPairsUntouched()
    {
        $uri = (new Uri('https://host/path?group.id=g1&flag&tag=x'))
            ->withQueryKeyValue('client.id', 'c1');

        $this->assertEquals('group.id=g1&flag&tag=x&client.id=c1', $uri->getQuery());
        $this->assertEquals('c1', $uri->getQueryPart('client.id'));
    }

    public function testWithQueryKeyValueReplacesInPlaceAndCollapsesRepeatedKeys()
    {
        $uri = (new Uri('https://host/path?tag=x&other=1&tag=y'))
            ->withQueryKeyValue('tag', 'z');

        $this->assertEquals('tag=z&other=1', $uri->getQuery());
        $this->assertEquals('z', $uri->getQueryPart('tag'));
    }

    public function testWithQueryKeyValueEncodesTheKey()
    {
        $uri = (new Uri('https://host/path'))->withQueryKeyValue('a key', 'a value');

        $this->assertEquals('a%20key=a%20value', $uri->getQuery());
        $this->assertEquals('a value', $uri->getQueryPart('a key'));
    }

    /**
     * A pre-signed URL only survives if order, repetition and encoding are left alone.
     */
    public function testPreSignedUrlRoundTrip()
    {
        $url = 'https://bucket.s3.amazonaws.com/key.txt'
            . '?X-Amz-Algorithm=AWS4-HMAC-SHA256'
            . '&X-Amz-Credential=AKIA%2F20260918%2Fus-east-1%2Fs3%2Faws4_request'
            . '&X-Amz-Date=20260918T000000Z'
            . '&X-Amz-Expires=900'
            . '&X-Amz-SignedHeaders=host'
            . '&X-Amz-Signature=abc123';

        $this->assertEquals($url, (string)(new Uri($url)));
    }
}
