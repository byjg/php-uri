---
sidebar_position: 4
---

# PSR-7 Methods

This library is fully compliant with [PSR-7 UriInterface](https://github.com/php-fig/http-message/blob/master/src/UriInterface.php). All methods are marked with the `#[Override]` attribute.

## Scheme

### getScheme()

Retrieve the scheme component of the URI.

```php
public function getScheme(): string
```

**Returns**: The URI scheme in lowercase (e.g., "http", "https", "ftp").

```php
$uri = Uri::getInstance("HTTPS://example.com");
echo $uri->getScheme(); // "https" (normalized to lowercase)
```

### withScheme()

Return an instance with the specified scheme.

```php
public function withScheme(string $scheme): UriInterface
```

**Parameters**:
- `$scheme` - The scheme to set (will be normalized to lowercase)

**Returns**: A new instance with the specified scheme.

```php
$uri = Uri::getInstance("http://example.com");
$uri = $uri->withScheme("https");
echo (string)$uri; // "https://example.com"
```

## Authority Components

### getHost()

Retrieve the host component of the URI.

```php
public function getHost(): string
```

```php
$uri = Uri::getInstance("https://example.com:8080/path");
echo $uri->getHost(); // "example.com"
```

### withHost()

Return an instance with the specified host.

```php
public function withHost(string $host): UriInterface
```

```php
$uri = $uri->withHost("newhost.com");
```

### getPort()

Retrieve the port component of the URI.

```php
public function getPort(): ?int
```

**Returns**: The port as an integer, or `null` if not set.

```php
$uri = Uri::getInstance("https://example.com:8080");
echo $uri->getPort(); // 8080

$uri = Uri::getInstance("https://example.com");
var_dump($uri->getPort()); // NULL
```

### withPort()

Return an instance with the specified port.

```php
public function withPort(?int $port): UriInterface
```

```php
$uri = $uri->withPort(9000);
$uri = $uri->withPort(null); // Remove port
```

### getUserInfo()

Retrieve the user information component of the URI.

```php
public function getUserInfo(): string
```

**Returns**: User information in "username[:password]" format. Password is URL-encoded.

```php
$uri = Uri::getInstance("https://user:pa&ss@example.com");
echo $uri->getUserInfo(); // "user:pa%26ss"

$uri = Uri::getInstance("https://user@example.com");
echo $uri->getUserInfo(); // "user"

$uri = Uri::getInstance("https://example.com");
echo $uri->getUserInfo(); // "" (empty string)
```

### withUserInfo()

Return an instance with the specified user information.

```php
public function withUserInfo(string $user, ?string $password = null): UriInterface
```

```php
$uri = Uri::getInstance("https://example.com")
    ->withUserInfo("john", "secret");
echo (string)$uri; // "https://john:secret@example.com"
```

:::warning
This method does NOT encode the password. If you pass an already-encoded password, it may result in double-encoding when the URI is serialized.
:::

### getAuthority()

Retrieve the authority component of the URI.

```php
public function getAuthority(): string
```

**Format**: `[userinfo@]host[:port]`

```php
$uri = Uri::getInstance("https://user:pass@example.com:8080/path");
echo $uri->getAuthority(); // "user:pass@example.com:8080"
```

## Path

### getPath()

Retrieve the path component of the URI.

```php
public function getPath(): string
```

```php
$uri = Uri::getInstance("https://example.com/some/path");
echo $uri->getPath(); // "/some/path"
```

### withPath()

Return an instance with the specified path.

```php
public function withPath(string $path): UriInterface
```

```php
$uri = $uri->withPath("/new/path");
```

## Query

### getQuery()

Retrieve the query string of the URI.

```php
public function getQuery(): string
```

**Returns**: The query string, RFC3986 encoded, without the leading "?".

```php
$uri = Uri::getInstance("https://example.com?name=John&age=30");
echo $uri->getQuery(); // "name=John&age=30"
```

### withQuery()

Return an instance with the specified query string.

```php
public function withQuery(string $query): UriInterface
```

**Parameters**:
- `$query` - The query string (without leading "?")

```php
$uri = $uri->withQuery("search=test&limit=10");
```

## Fragment

### getFragment()

Retrieve the fragment component of the URI.

```php
public function getFragment(): string
```

**Returns**: The URI fragment without the leading "#".

```php
$uri = Uri::getInstance("https://example.com#section");
echo $uri->getFragment(); // "section"
```

### withFragment()

Return an instance with the specified URI fragment.

```php
public function withFragment(string $fragment): UriInterface
```

```php
$uri = $uri->withFragment("top");
```

## String Conversion

### __toString()

Return the string representation of the URI.

```php
public function __toString(): string
```

**Format**: `[scheme]://[authority][path]?[query]#[fragment]`

```php
$uri = Uri::getInstance("https://user:pass@example.com:8080/path?query=value#fragment");
echo (string)$uri;
// "https://user:pass@example.com:8080/path?query=value#fragment"
```

---

[← Previous: Additional Methods](additional-methods.md) | [Next: Factory Methods →](factory-methods.md)
