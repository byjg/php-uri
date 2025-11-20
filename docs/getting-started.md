---
sidebar_position: 1
---

# Getting Started

A PHP implementation of PSR-7 UriInterface with additional utility methods.

## Installation

Install via Composer:

```bash
composer require "byjg/uri"
```

## Requirements

- PHP 8.1, 8.2, 8.3, 8.4, or 8.5
- psr/http-message (^1.0|^1.1|^2.0)

## Basic Usage

```php
use ByJG\Util\Uri;

// Create a URI from a string
$uri = Uri::getInstance("https://user:pass@example.com:8080/path?query=value#fragment");

// Access components
echo $uri->getScheme();    // "https"
echo $uri->getHost();      // "example.com"
echo $uri->getPort();      // 8080
echo $uri->getPath();      // "/path"
echo $uri->getQuery();     // "query=value"
echo $uri->getFragment();  // "fragment"

// Get user credentials
echo $uri->getUsername();  // "user"
echo $uri->getPassword();  // "pass"
```

## Immutability

The `Uri` class follows an immutable design pattern. All modification methods return a new instance:

```php
$uri = Uri::getInstance("https://example.com/old-path");

// This creates a new instance, original is unchanged
$newUri = $uri->withPath("/new-path");

echo $uri->getPath();      // "/old-path"
echo $newUri->getPath();   // "/new-path"
```

## Key Features

- ✅ Fully compliant with [PSR-7 UriInterface](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php)
- ✅ Additional utility methods via `CustomUriInterface`
- ✅ RFC3986 URI specification support
- ✅ Type-safe with PHP 8.1+ strict typing
- ✅ Immutable value object pattern
