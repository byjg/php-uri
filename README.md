# PSR-7 URI Implementation

[![Build Status](https://github.com/byjg/php-uri/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-uri/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-uri/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-uri.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-uri.svg)](https://github.com/byjg/uri/releases/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/uri/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/uri/?branch=master)

A PHP implementation of PSR-7 UriInterface with additional utility methods.

## Features

* Fully compliant with [PSR-7 UriInterface](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php)
* Includes additional utility methods via CustomUriInterface
* Supports RFC3986 URI specification
* Type-safe implementation with PHP 8 attributes and strict typing
* Immutable class design pattern

## URI Encoding Behavior

PSR-7 requires URI compliant with RFC3986. This means the URI output will always be URL encoded. This applies to both creating a new instance and the string representation.

Examples:

```php
// Creating a URI with special characters in the password
$uri = \ByJG\Util\Uri::getInstance("https://user:pa&@host");
print((string)$uri); // Will print "https://user:pa%26@host"

// Creating a URI with already encoded characters
$uri = \ByJG\Util\Uri::getInstance("https://user:pa%26@host");
print((string)$uri); // Will print "https://user:pa%26@host"

// Using withUserInfo with unencoded password
$uri = \ByJG\Util\Uri::getInstance("https://host")
    ->withUserInfo("user", "pa&");
print((string)$uri); // Will print "https://user:pa&@host"

// Using withUserInfo with already encoded password
$uri = \ByJG\Util\Uri::getInstance("https://host")
    ->withUserInfo("user", "pa%26");
print((string)$uri); // Will print "https://user:pa%2526@host"
```

## Additional Methods

This implementation extends PSR-7 UriInterface with the following additional methods through `\ByJG\Util\CustomUriInterface`:

| Method                                                                         | Description                           |
|--------------------------------------------------------------------------------|---------------------------------------|
| `getUsername(): ?string`                                                       | Get the username component of the URI |
| `getPassword(): ?string`                                                       | Get the password component of the URI |
| `getQueryPart(string $key): ?string`                                           | Get a specific query parameter by key |
| `withQueryKeyValue(string $key, string $value, bool $isEncoded = false): self` | Add or update a query parameter       |
| `hasQueryKey(string $key): bool`                                               | Check if a query parameter exists     |

## Static Factory Methods

The class provides convenient static factory methods:

```php
// Create from string
$uri = Uri::getInstance("https://example.com/path?query=value#fragment");

// Create from another UriInterface
$uri2 = Uri::getInstance($uri);
```

## Documentation

- [Getting Started](docs/getting-started.md)
- [URI Encoding Behavior](docs/uri-encoding.md)
- [Additional Methods](docs/additional-methods.md)
- [PSR-7 Methods](docs/psr7-methods.md)
- [Factory Methods](docs/factory-methods.md)
- [Examples](docs/examples.md)

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
