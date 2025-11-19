---
sidebar_position: 3
---

# Additional Methods

Beyond the standard PSR-7 interface, this implementation provides additional utility methods through `\ByJG\Util\CustomUriInterface`.

## Username and Password Access

### getUsername()

Get the username component separately from the password.

```php
public function getUsername(): ?string
```

**Returns**: The username, or `null` if not set.

```php
$uri = Uri::getInstance("https://john:secret@example.com");
echo $uri->getUsername(); // "john"

$uri = Uri::getInstance("https://example.com");
var_dump($uri->getUsername()); // NULL
```

### getPassword()

Get the password component of the URI.

```php
public function getPassword(): ?string
```

**Returns**: The password, or `null` if not set.

```php
$uri = Uri::getInstance("https://john:secret@example.com");
echo $uri->getPassword(); // "secret"

$uri = Uri::getInstance("https://john@example.com");
var_dump($uri->getPassword()); // NULL
```

## Query Parameter Manipulation

### getQueryPart()

Retrieve a specific query parameter value by key.

```php
public function getQueryPart(string $key): ?string
```

**Parameters**:
- `$key` - The query parameter name

**Returns**:
- The parameter value
- Empty string `""` if the key exists with no value
- `null` if the key doesn't exist

```php
$uri = Uri::getInstance("https://example.com?name=John&empty=&flag");

echo $uri->getQueryPart("name");   // "John"
echo $uri->getQueryPart("empty");  // ""
var_dump($uri->getQueryPart("missing")); // NULL
```

### withQueryKeyValue()

Add or update a single query parameter.

```php
public function withQueryKeyValue(
    string $key,
    string $value,
    bool $isEncoded = false
): self
```

**Parameters**:
- `$key` - The query parameter name
- `$value` - The parameter value
- `$isEncoded` - Whether the value is already URL-encoded (default: `false`)

**Returns**: A new `Uri` instance with the updated query parameter.

```php
$uri = Uri::getInstance("https://example.com?existing=value");

// Add a new parameter
$uri = $uri->withQueryKeyValue("name", "John Doe");
echo $uri->getQuery(); // "existing=value&name=John+Doe"

// Update existing parameter
$uri = $uri->withQueryKeyValue("existing", "new-value");
echo $uri->getQuery(); // "existing=new-value&name=John+Doe"

// With pre-encoded value
$uri = $uri->withQueryKeyValue("encoded", "already%20encoded", true);
echo $uri->getQuery(); // "existing=new-value&name=John+Doe&encoded=already encoded"
```

:::info
When `$isEncoded = true`, the value is decoded using `rawurldecode()` before storage. When `false`, the value is stored as-is.
:::

### hasQueryKey()

Check if a query parameter exists.

```php
public function hasQueryKey(string $key): bool
```

**Parameters**:
- `$key` - The query parameter name

**Returns**: `true` if the parameter exists, `false` otherwise.

```php
$uri = Uri::getInstance("https://example.com?name=John&empty=");

var_dump($uri->hasQueryKey("name"));    // true
var_dump($uri->hasQueryKey("empty"));   // true
var_dump($uri->hasQueryKey("missing")); // false
```

## Method Chaining

All methods returning `self` can be chained for fluent API usage:

```php
$uri = Uri::getInstance("https://example.com")
    ->withQueryKeyValue("page", "1")
    ->withQueryKeyValue("limit", "10")
    ->withQueryKeyValue("sort", "name");

echo (string)$uri;
// "https://example.com?page=1&limit=10&sort=name"
```
