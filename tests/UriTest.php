<?php

namespace ByJG\Util;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function uriProvider()
    {
        return [
            [ // #0
                'http://username:password@hostname/path?arg=value#anchor',
                [
                    'Scheme' => 'http',
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
                'http://username:password@hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'http',
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
                'http://hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'http',
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
                'http://username@hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'http',
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
                'http://email@host.com.br:password@hostname/path/path2?arg=value&arg2=value2#anchor',
                [
                    'Scheme' => 'http',
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
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Path' => null,
                    'Query' => null,
                    'Fragment' => null,
                    'Authority' => 'hostname.com:443'
                ]
            ],
            [ // #8
                'http://hostname.com/#anchor',
                [
                    'Scheme' => 'http',
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
                'mysql://root:password@host-10.com:3306/database?extraparam=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => 'password',
                    'Userinfo' => 'root:password',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extraparam=10',
                    'Fragment' => null,
                    'Authority' => 'root:password@host-10.com:3306'
                ]
            ],
            [ // #10
                'mysql://ro@11!%&*(ot:pass@(*&!$$word@host-10.com:3306/database?extraparam=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'ro@11!%&*(ot',
                    'Password' => 'pass@(*&!$$word',
                    'Userinfo' => 'ro@11!%&*(ot:pass%40%28%2A%26%21%24%24word',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extraparam=10',
                    'Fragment' => null,
                    'Authority' => 'ro@11!%&*(ot:pass%40%28%2A%26%21%24%24word@host-10.com:3306',
                    'ToString' => 'mysql://ro@11!%&*(ot:pass%40%28%2A%26%21%24%24word@host-10.com:3306/database?extraparam=10'
                ]
            ],
            [ // #11
                'mysql://root@host-10.com:3306/database?extraparam=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => null,
                    'Userinfo' => 'root',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extraparam=10',
                    'Fragment' => null,
                    'Authority' => 'root@host-10.com:3306'
                ]
            ],
            [ // #12
                'mysql://root@host-10.com:3306/database?extraparam=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => 'root',
                    'Password' => null,
                    'Userinfo' => 'root',
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extraparam=10',
                    'Fragment' => null,
                    'Authority' => 'root@host-10.com:3306'
                ]
            ],
            [ // #13
                'mysql://host-10.com:3306/database?extraparam=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => '/database',
                    'Query' => 'extraparam=10',
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
                    'Authority' => 'host-10.com:3306'
                ]
            ],
            [ // #16
                'mysql://host-10.com:3306?extraparam=10',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => null,
                    'Query' => 'extraparam=10',
                    'Fragment' => null,
                    'Authority' => 'host-10.com:3306'
                ]
            ],
            [ // #17
                'mysql://host-10.com:3306?extraparam=10&other=20',
                [
                    'Scheme' => 'mysql',
                    'Username' => null,
                    'Password' => null,
                    'Userinfo' => null,
                    'Host' => 'host-10.com',
                    'Port' => 3306,
                    'Path' => null,
                    'Query' => 'extraparam=10&other=20',
                    'Fragment' => null,
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
                    'Path' => null,
                    'Query' => null,
                    'Fragment' => null,
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
                    'Path' => null,
                    'Query' => null,
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
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
                    'Fragment' => null,
                    'Authority' => 'root@host-10.com:3306'
                ]
            ],
            [ // #27
                'http://user:O=+9zLZ}%{z+:tC@host/path',
                [
                    'Scheme' => 'http',
                    'Username' => "user",
                    'Password' => "O=+9zLZ}%{z+:tC",
                    'Userinfo' => "user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC",
                    'Host' => "host",
                    'Port' => null,
                    'Path' => '/path',
                    'Query' => null,
                    'Fragment' => null,
                    'Authority' => "user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC@host",
                    'ToString' => 'http://user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC@host/path',
                ]
            ],
        ];
    }
    
    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseScheme($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Scheme"], $uri->getScheme());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseUsername($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Username"], $uri->getUsername());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParsePassword($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Password"], $uri->getPassword());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseUserinfo($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Userinfo"], $uri->getUserinfo());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseHost($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Host"], $uri->getHost());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParsePort($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Port"], $uri->getPort());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParsePath($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Path"], $uri->getPath());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseQuery($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Query"], $uri->getQuery());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseFragment($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Fragment"], $uri->getFragment());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
    public function testParseAuthority($uriStr, $assertFields = null)
    {
        $uri = new Uri($uriStr);
        $this->assertEquals($assertFields["Authority"], $uri->getAuthority());
    }

    /**
     * @dataProvider uriProvider
     * @param $uriStr
     * @param null $assertFields
     */
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
            'http://host.com:1234',
            (new Uri())
                ->withScheme('http')
                ->withHost('host.com')
                ->withPort('1234')
        );
    }

    public function testMountUrl2()
    {
        $uri = new Uri('/some/relative/path');

        $this->assertEquals(
            'http://host.com/some/relative/path',
            $uri
                ->withScheme('http')
                ->withHost('host.com')
                ->__toString()
        );
    }

    public function testChangeParameters()
    {
        $uri = new Uri('http://foo-host.com/path?key=value&otherkey=othervalue#fragment');

        $this->assertEquals(
            'http://bar.net/otherpath?key=newvalue&otherkey=othervalue&newkey=value#fragment',
            $uri
                ->withHost('bar.net')
                ->withPath('/otherpath')
                ->withQueryKeyValue('key', 'newvalue')
                ->withQueryKeyValue('newkey', 'value')
        );
    }

    public function testWithUrlEncoding()
    {
        $uri = (new Uri('http://example.com/path/to?q=foo%20bar#section-42'))
            ->withUserInfo('user', "O=+9zLZ}%{z+:tC");

        $this->assertEquals("user", $uri->getUsername());
        $this->assertEquals("O=+9zLZ}%{z+:tC", $uri->getPassword());
        $this->assertEquals('user:O%3D%2B9zLZ%7D%25%7Bz%2B%3AtC', $uri->getUserInfo());
    }
}
