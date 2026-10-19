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

**Returns**: User information in "username[:password]" format. The `:password` part is written
whenever there is a password, even `"0"` or an empty one (`user:`), and left out when the
password is `null`. The username and the password are
each percent-encoded with `rawurlencode()`, so a `:` or `@` inside them cannot be read as a
delimiter (RFC 3986 section 3.2.1). Use `getUsername()` and `getPassword()` for the decoded values.

```php
$uri = Uri::getInstance("https://user:pa&ss@example.com");
echo $uri->getUserInfo(); // "user:pa%26ss"

$uri = Uri::getInstance("https://user@example.com");
echo $uri->getUserInfo(); // "user"

$uri = Uri::getInstance("https://user:0@example.com");
echo $uri->getUserInfo(); // "user:0"

$uri = Uri::getInstance("https://email@host.com:pass@example.com");
echo $uri->getUserInfo(); // "email%40host.com:pass"

// Password only (empty user), e.g. Redis AUTH
$uri = Uri::getInstance("redis://:mypassword@host:6379");
echo $uri->getUserInfo(); // ":mypassword"

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

// A null password writes no ":"; an empty password writes "user:"
echo (string)Uri::getInstance("https://example.com")->withUserInfo("john");     // "https://john@example.com"
echo (string)Uri::getInstance("https://example.com")->withUserInfo("john", ""); // "https://john:@example.com"

// An empty user removes the user information (PSR-7), the password included
$uri = Uri::getInstance("redis://user:pw@host:6379")
    ->withUserInfo("", "mypassword");
echo (string)$uri;           // "redis://host:6379"
var_dump($uri->getUsername()); // NULL
var_dump($uri->getPassword()); // NULL

// The user and the password are encoded on output
$uri = Uri::getInstance("https://example.com")
    ->withUserInfo("us:er", "p@ss");
echo (string)$uri; // "https://us%3Aer:p%40ss@example.com"
```

:::warning
Pass the user and the password decoded: they are stored as given and encoded on output. An
already-encoded value is encoded again (`pa%26` becomes `pa%2526`).
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
