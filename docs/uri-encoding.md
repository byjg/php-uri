---
sidebar_position: 2
---

# URI Encoding Behavior

PSR-7 requires URIs to be compliant with RFC3986, which means URI output will always be properly URL encoded.

## Password Encoding

When creating URIs with special characters in passwords, they are automatically encoded:

```php
// Creating a URI with special characters in the password
$uri = Uri::getInstance("https://user:pa&@host");
echo (string)$uri; // "https://user:pa%26@host"

// Creating a URI with already encoded characters
$uri = Uri::getInstance("https://user:pa%26@host");
echo (string)$uri; // "https://user:pa%26@host"
```

## Using withUserInfo

The `withUserInfo()` method does NOT encode the password automatically:

```php
// Using withUserInfo with unencoded password
$uri = Uri::getInstance("https://host")
    ->withUserInfo("user", "pa&");
echo (string)$uri; // "https://user:pa&@host"

// Using withUserInfo with already encoded password
$uri = Uri::getInstance("https://host")
    ->withUserInfo("user", "pa%26");
echo (string)$uri; // "https://user:pa%2526@host"
```

:::warning
Be careful when using `withUserInfo()` with pre-encoded passwords, as this can lead to double-encoding.
:::

## Query Parameter Encoding

Query parameters are encoded according to RFC3986:

```php
$uri = Uri::getInstance("https://example.com")
    ->withQuery("name=John Doe&email=john@example.com");

echo $uri->getQuery();
// "name=John+Doe&email=john%40example.com"
```

## How Encoding Works Internally

1. **During parsing**: Passwords are decoded using `rawurldecode()`
2. **During output**: Passwords in `getUserInfo()` are re-encoded with `rawurlencode()`
3. **Query strings**: Built using `http_build_query()` with `PHP_QUERY_RFC3986` flag

This ensures that URIs can be parsed and re-serialized idempotently (parsing the output produces the same URI).
