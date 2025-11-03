---
sidebar_position: 6
---

# Examples

Practical examples demonstrating common use cases.

## Building URIs from Scratch

```php
use ByJG\Util\Uri;

$uri = Uri::getInstance()
    ->withScheme("https")
    ->withHost("api.example.com")
    ->withPort(443)
    ->withPath("/v1/users")
    ->withQueryKeyValue("page", "1")
    ->withQueryKeyValue("limit", "20");

echo (string)$uri;
// "https://api.example.com:443/v1/users?page=1&limit=20"
```

## Database Connection Strings

### MySQL

```php
$uri = Uri::getInstance("mysql://root:password@localhost:3306/mydb");

echo $uri->getScheme();    // "mysql"
echo $uri->getUsername();  // "root"
echo $uri->getPassword();  // "password"
echo $uri->getHost();      // "localhost"
echo $uri->getPort();      // 3306
echo $uri->getPath();      // "/mydb"
```

### PostgreSQL with Query Parameters

```php
$uri = Uri::getInstance("postgresql://user:pass@host/database")
    ->withQueryKeyValue("sslmode", "require")
    ->withQueryKeyValue("connect_timeout", "10");

echo (string)$uri;
// "postgresql://user:pass@host/database?sslmode=require&connect_timeout=10"
```

### SQLite

```php
// Windows path
$uri = Uri::getInstance("sqlite://C:\\data\\mydb.sqlite");

// Unix path
$uri = Uri::getInstance("sqlite:///var/lib/data/mydb.sqlite");
```

## Modifying Existing URIs

```php
$uri = Uri::getInstance("http://example.com/old-path?old=param");

// Change to HTTPS
$uri = $uri->withScheme("https");

// Change path
$uri = $uri->withPath("/new-path");

// Replace query string entirely
$uri = $uri->withQuery("new=param&another=value");

echo (string)$uri;
// "https://example.com/new-path?new=param&another=value"
```

## Working with Query Parameters

### Adding Parameters

```php
$uri = Uri::getInstance("https://api.example.com/search");

$uri = $uri
    ->withQueryKeyValue("q", "search term")
    ->withQueryKeyValue("category", "books")
    ->withQueryKeyValue("sort", "price");

echo (string)$uri;
// "https://api.example.com/search?q=search+term&category=books&sort=price"
```

### Reading Parameters

```php
$uri = Uri::getInstance("https://example.com?name=John&age=30&active");

echo $uri->getQueryPart("name");  // "John"
echo $uri->getQueryPart("age");   // "30"
var_dump($uri->hasQueryKey("active")); // true
var_dump($uri->getQueryPart("active")); // "" (empty string, key exists)
var_dump($uri->getQueryPart("missing")); // NULL
```

### Updating Parameters

```php
$uri = Uri::getInstance("https://example.com?page=1&limit=10");

// Update existing parameter
$uri = $uri->withQueryKeyValue("page", "2");

echo $uri->getQuery(); // "page=2&limit=10"
```

## Handling Special Characters

### Email as Username

```php
$uri = Uri::getInstance("ftp://user@example.com:password@ftp.server.com/path");

echo $uri->getUsername(); // "user@example.com"
echo $uri->getPassword(); // "password"
echo $uri->getHost();     // "ftp.server.com"
```

### Special Characters in Password

```php
// Automatically encoded during parsing
$uri = Uri::getInstance("https://user:p@ss&word!@example.com");

echo $uri->getUserInfo(); // "user:p%40ss%26word%21"
echo $uri->getPassword(); // "p@ss&word!" (decoded internally)
```

## File URIs

### Local File Path

```php
// Unix
$uri = Uri::getInstance("file:///home/user/documents/file.txt");
echo $uri->getPath(); // "/home/user/documents/file.txt"

// Windows
$uri = Uri::getInstance("file://C:\\Users\\John\\file.txt");
echo $uri->getPath(); // "C:\\Users\\John\\file.txt"
```

## API URL Building

```php
$baseUri = Uri::getInstance("https://api.github.com");

// Build endpoint URL
$uri = $baseUri
    ->withPath("/repos/byjg/php-uri/issues")
    ->withQueryKeyValue("state", "open")
    ->withQueryKeyValue("labels", "bug")
    ->withQueryKeyValue("per_page", "50");

echo (string)$uri;
// "https://api.github.com/repos/byjg/php-uri/issues?state=open&labels=bug&per_page=50"
```

## Parsing and Extracting Information

```php
$url = "https://john:secret@example.com:8080/path/to/resource?search=test&filter=active#section";
$uri = Uri::getInstance($url);

// Extract all components
$components = [
    'scheme' => $uri->getScheme(),       // "https"
    'user' => $uri->getUsername(),       // "john"
    'pass' => $uri->getPassword(),       // "secret"
    'host' => $uri->getHost(),           // "example.com"
    'port' => $uri->getPort(),           // 8080
    'path' => $uri->getPath(),           // "/path/to/resource"
    'query' => $uri->getQuery(),         // "search=test&filter=active"
    'fragment' => $uri->getFragment(),   // "section"
    'authority' => $uri->getAuthority(), // "john:secret@example.com:8080"
];

print_r($components);
```

## Immutability Example

```php
$original = Uri::getInstance("https://example.com/path");

// Each modification returns a new instance
$modified1 = $original->withPath("/new-path");
$modified2 = $original->withScheme("http");

echo $original->getPath();    // "/path" (unchanged)
echo $original->getScheme();  // "https" (unchanged)

echo $modified1->getPath();   // "/new-path"
echo $modified2->getScheme(); // "http"
```

## Type Conversion

```php
// From another PSR-7 implementation
$psr7Uri = new \GuzzleHttp\Psr7\Uri("https://example.com");
$uri = Uri::getInstance($psr7Uri);

// Back to string
$uriString = (string)$uri;

// Re-parse
$newUri = Uri::getInstance($uriString);
```

---

[← Previous: Factory Methods](factory-methods.md) | [Back to Index](../README.md)
