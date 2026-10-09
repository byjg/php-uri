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

The key is matched against the query exactly as it was written, so keys containing
characters that are not valid in a PHP variable name resolve as well:

```php
$uri = Uri::getInstance("https://host/p?session.timeout.ms=6000&my%20key=a%20value");

echo $uri->getQueryPart("session.timeout.ms"); // "6000"
echo $uri->getQueryPart("my key");             // "a value"
```

If the key is repeated, the value of the **last** occurrence is returned. Use
[`getQueryParts()`](#getqueryparts) to read every value.

:::warning
For backward compatibility, a key that is not found falls back to the `parse_str()` index,
so the PHP-mangled name still resolves: `getQueryPart("session_timeout_ms")` returns
`"6000"` too. **This fallback is deprecated and will be removed in 8.0** — ask for the key
as it is written in the query.
:::

### getQueryParts()

Retrieve **every** value of a query parameter, in the order they appear.

```php
public function getQueryParts(string $key): array
```

**Parameters**:
- `$key` - The query parameter name

**Returns**: A list of values. An empty array if the key doesn't exist, and `[""]` for a key
present with no value.

```php
$uri = Uri::getInstance("https://example.com?tag=x&page=1&tag=y&tag=z");

$uri->getQueryParts("tag");     // ["x", "y", "z"]
$uri->getQueryParts("page");    // ["1"]
$uri->getQueryParts("missing"); // []
```

This is the only way to read a repeated key, since `getQueryPart()` returns a single value.

:::info
Unlike `getQueryPart()`, this method never falls back on the deprecated `parse_str()`
mangled key names: the key must be written exactly as it appears in the query.
:::

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
echo $uri->getQuery(); // "existing=value&name=John%20Doe"

// Update existing parameter, in place
$uri = $uri->withQueryKeyValue("existing", "new-value");
echo $uri->getQuery(); // "existing=new-value&name=John%20Doe"

// With pre-encoded value
$uri = $uri->withQueryKeyValue("encoded", "already%20encoded", true);
echo $uri->getQuery(); // "existing=new-value&name=John%20Doe&encoded=already%20encoded"
```

:::info
When `$isEncoded = true`, the value is decoded using `rawurldecode()` before storage. When `false`, the value is stored as-is.
:::

:::info
An existing key is replaced where it already is, keeping the position of the other
parameters. If the key appears more than once, the repeated occurrences are dropped.
:::

### hasQueryKey()

Check if a query parameter exists.

:::warning
As with `getQueryPart()`, a key that is not found falls back to the deprecated `parse_str()`
index. That fallback will be removed in 8.0.
:::

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
