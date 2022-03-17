# Uri class

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/uri/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/uri/?branch=master)
[![Build Status](https://github.com/byjg/uri/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/uri/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/uri/)
[![GitHub license](https://img.shields.io/github/license/byjg/uri.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/uri.svg)](https://github.com/byjg/uri/releases/)


An implementation of PSR-7 UriInterface

PSR-7 requires URI compliant to RFC3986. It means the URI output will be always url encoded. The same is valid to create a new instance.
The only way to store the plain password is using ->withUserInfo()

For example:

```php
$uri = \ByJG\Util\Uri::getInstanceFromString("http://user:pa&@host");
print((string)$uri); // Will print "http://user:pa%26@host"

$uri = \ByJG\Util\Uri::getInstanceFromString("http://user:pa%26@host");
print((string)$uri); // Will print "http://user:pa%26@host"

$uri = \ByJG\Util\Uri::getInstanceFromString("http://host")
    ->withUserInfo("user", "pa%26");
print((string)$uri); // Will print "http://user:pa%2526@host"
```

## Custom methods

This class is fully compliant with the PSR UriInterface (PSR-7), but it implements some useful extra methods in
the interface \ByJG\Util\CustomUriInterface:

- getUsername()
- getPassword()
- getQueryPart($key)
- withQueryKeyValue($key, $value, $encode = true)


More information about UriInterface:
https://github.com/php-fig/http-message/blob/master/src/UriInterface.php

----
[Open source ByJG](http://opensource.byjg.com)
