---
sidebar_key: uri
sidebar_category: http-apis
tags: [php, http]
---

# PSR-7 URI Implementation

A PSR-7 URI that also works as a connection string: the object behind the `mysql://`, `amqp://` or `s3://` URIs of the ByJG components.

[![Sponsor](https://img.shields.io/badge/Sponsor-%23ea4aaa?logo=githubsponsors&logoColor=white&labelColor=0d1117)](https://github.com/sponsors/byjg)
[![Build Status](https://github.com/byjg/php-uri/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-uri/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](https://opensource.byjg.com)
[![Install MCP Server](https://img.shields.io/badge/Install-MCP_Server-8A2BE2?logo=modelcontextprotocol&logoColor=white)](https://opensource.byjg.com/docs/ai/mcpserver-byjg-docs/)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-uri/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-uri.svg)](https://opensource.byjg.com/license/)
[![GitHub release](https://img.shields.io/github/release/byjg/php-uri.svg)](https://github.com/byjg/php-uri/releases/)

## Why

Connection strings look like URIs, but a strict PSR-7 implementation mangles them: it drops
the standard port (`https://host:443` loses `443`) and cannot represent a host and port
without a scheme (`kafka:9092`). Passwords with special characters need encoding by hand.

This URI keeps what a connection string needs, and adds what PSR-7 leaves out:

```php
$uri = Uri::getInstance('mysql://user:p%40ss@db:3306/shop?charset=utf8');

$uri->getPassword();             // "p@ss", decoded
$uri->getQueryPart('charset');   // "utf8"
$uri->withQueryKeyValue('timeout', '5');
```

The same class reads the connection strings of the ByJG components (databases, NoSQL
stores, message queues, mail, HTTP), so a connection is always configured the same way.

## Features

* Implements [PSR-7 UriInterface](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php), with two deliberate
  differences for connection strings (see [PSR-7 differences](#psr-7-differences))
* Implements the [PSR-17](https://www.php-fig.org/psr/psr-17/) `UriFactoryInterface` (`UriFactory`)
* Includes additional utility methods via CustomUriInterface
* Supports RFC3986 URI specification
* Type-safe implementation with PHP 8 attributes and strict typing
* Immutable class design pattern

## PSR-7 differences

The URI is also used for connection strings (`mysql://host:3306/db`, `h:9092`), so two PSR-7 recommendations are not followed:

* **The standard port is kept.** `https://host:443` keeps `443` in `getPort()`, `getAuthority()` and the string (PSR-7
  SHOULD return null and leave it out).
* **A URI without a scheme is written without `//`.** `new Uri('h:9092')` is read as host `h` and port `9092` and
  written back as `h:9092`, not `//h:9092`. A URI with a scheme always has `//` after it, even without a host
  (`sqlite:///tmp/db`).

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
print((string)$uri); // Will print "https://user:pa%26@host"

// Using withUserInfo with already encoded password
$uri = \ByJG\Util\Uri::getInstance("https://host")
    ->withUserInfo("user", "pa%26");
print((string)$uri); // Will print "https://user:pa%2526@host"

// The username is decoded by getUsername() and encoded in the string form, like the password
$uri = \ByJG\Util\Uri::getInstance("https://email@host.com:pass@host");
print($uri->getUsername()); // Will print "email@host.com"
print((string)$uri); // Will print "https://email%40host.com:pass@host"
```

## Additional Methods

This implementation extends PSR-7 UriInterface with the following additional methods through `\ByJG\Util\CustomUriInterface`:

| Method                                                                         | Description                           |
|--------------------------------------------------------------------------------|---------------------------------------|
| `getUsername(): ?string`                                                       | Get the decoded username of the URI   |
| `getPassword(): ?string`                                                       | Get the decoded password of the URI   |
| `getQueryPart(string $key): ?string`                                           | Get a specific query parameter by key |
| `getQueryParts(string $key): array`                                            | Get every value of a query parameter  |
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

Code that receives a PSR-17 `UriFactoryInterface` gets the same `Uri` from `UriFactory`:

```php
$uri = (new \ByJG\Util\UriFactory())->createUri("https://example.com/path");
```

## Documentation

- [Getting Started](docs/getting-started.md)
- [URI Encoding Behavior](docs/uri-encoding.md)
- [Additional Methods](docs/additional-methods.md)
- [PSR-7 Methods](docs/psr7-methods.md)
- [Factory Methods](docs/factory-methods.md)
- [Examples](docs/examples.md)

## Installation

```bash
composer require "byjg/uri"
```

## Running Tests

```bash
vendor/bin/phpunit
```

----
[Open source ByJG](https://opensource.byjg.com)
