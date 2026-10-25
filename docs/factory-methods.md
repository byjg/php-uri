---
sidebar_position: 5
---

# Factory Methods

The `Uri` class provides static factory methods for flexible object creation.

## getInstance()

The primary factory method that accepts multiple input types.

```php
public static function getInstance(
    string|UriInterface|null $uri = null
): static
```

**Parameters**:
- `$uri` - Can be a string, a `UriInterface` instance, or `null`

**Returns**: A new instance of the class it is called on - `Uri`, or the subclass when
called as `MyUri::getInstance()`

### Create from String

```php
$uri = Uri::getInstance("https://example.com/path?query=value#fragment");
```

### Create from UriInterface

Useful for converting other PSR-7 URI implementations:

```php
$otherUri = new SomeOtherPsr7Uri("https://example.com");
$uri = Uri::getInstance($otherUri);
```

:::info
When passed a `UriInterface`, the method converts it to a string first, then parses it as a new `Uri` instance.
:::

### Create Empty URI

```php
$uri = Uri::getInstance();
// or
$uri = Uri::getInstance(null);
```

## Removed Methods

`getInstanceFromString()` and `getInstanceFromUri()`, deprecated in 6.0, were removed in 7.0.
Use `getInstance()`, which accepts a string, a `UriInterface` or `null`:

```php
$uri = Uri::getInstance("https://example.com");   // was Uri::getInstanceFromString(...)
$uri = Uri::getInstance($otherUri);               // was Uri::getInstanceFromUri(...)
```

## Constructor

You can also use the constructor directly:

```php
$uri = new Uri("https://example.com/path");
```

However, the static factory method `getInstance()` is recommended for consistency and flexibility.

## UriFactory (PSR-17)

`ByJG\Util\UriFactory` implements the PSR-17 `UriFactoryInterface`, for code and containers
that create URIs through the standard factory instead of a concrete class:

```php
use ByJG\Util\UriFactory;
use Psr\Http\Message\UriFactoryInterface;

$factory = new UriFactory();               // a UriFactoryInterface
$uri = $factory->createUri('mysql://user:pass@db:3306/shop');
```

`createUri()` returns the same `Uri` as `new Uri($string)`; `createUri()` with no argument
returns an empty URI.

:::note Invalid URIs are not rejected
PSR-17 says `createUri()` must throw an `InvalidArgumentException` when the URI cannot be
parsed. The `Uri` parser is permissive and accepts any string, so `createUri()` never
throws: `http://[bad`, `http://host:99999` or a string with a newline are kept as given.
Validate the URI before creating it when the input is not trusted.
:::
