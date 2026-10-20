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
