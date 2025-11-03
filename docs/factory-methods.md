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
): UriInterface
```

**Parameters**:
- `$uri` - Can be a string, a `UriInterface` instance, or `null`

**Returns**: A new `Uri` instance

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

## Deprecated Methods

The following methods are deprecated but still available for backward compatibility:

### getInstanceFromString()

```php
/**
 * @deprecated Use getInstance() instead
 */
public static function getInstanceFromString(
    string|null $uriString = null
): UriInterface
```

**Migration**:
```php
// Old way
$uri = Uri::getInstanceFromString("https://example.com");

// New way
$uri = Uri::getInstance("https://example.com");
```

### getInstanceFromUri()

```php
/**
 * @deprecated Use getInstance() instead
 */
public static function getInstanceFromUri(UriInterface $uri): UriInterface
```

**Migration**:
```php
// Old way
$uri = Uri::getInstanceFromUri($otherUri);

// New way
$uri = Uri::getInstance($otherUri);
```

## Constructor

You can also use the constructor directly:

```php
$uri = new Uri("https://example.com/path");
```

However, the static factory method `getInstance()` is recommended for consistency and flexibility.
