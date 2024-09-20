# Uri class

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/uri/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/uri/?branch=master)
[![Build Status](https://github.com/byjg/uri/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/uri/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](https://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/uri/)
[![GitHub license](https://img.shields.io/github/license/byjg/uri.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/uri.svg)](https://github.com/byjg/uri/releases/)

An implementation of PSR-7 UriInterface

PSR-7 requires URI compliant with RFC3986. It means the URI output will always be URL encoded. The same is valid for creating a new instance.
The only way to store the plain password is using `Uri::withUserInfo()`

For example:

```php
$uri = \ByJG\Util\Uri::getInstanceFromString("https://user:pa&@host");
print((string)$uri); // Will print "https://user:pa%26@host"

$uri = \ByJG\Util\Uri::getInstanceFromString("https://user:pa%26@host");
print((string)$uri); // Will print "https://user:pa%26@host"

$uri = \ByJG\Util\Uri::getInstanceFromString("https://host")
    ->withUserInfo("user", "pa%26");
print((string)$uri); // Will print "https://user:pa%2526@host"
```

## Custom methods

This class fully complies with the PSR UriInterface (PSR-7) and also implements some additional useful methods in the `\ByJG\Util\CustomUriInterface` interface:

- `getUsername()` 
- `getPassword()` 
- `getQueryPart($key)`
- `withQueryKeyValue($key, $value, $encode = true)`
- `hasQueryKey(string $key): bool;`

For more information about UriInterface, visit:
[https://github.com/php-fig/http-message/blob/master/src/UriInterface.php](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php)

## Install

```bash
composer require "byjg/uri"
```

## Unit tests

```bash
vendor/bin/phpunit
```

## Dependencies

```mermaid
flowchart TD
    byjg/uri --> psr/http-message
```

----
[Open source ByJG](https://opensource.byjg.com)
