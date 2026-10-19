---
sidebar_position: 2
---

# URI Encoding Behavior

PSR-7 requires URIs to be compliant with RFC3986, which means URI output will always be properly URL encoded.

## Userinfo Encoding

The username and the password are stored decoded (`getUsername()`, `getPassword()`) and each
one is percent-encoded with `rawurlencode()` in `getUserInfo()`, `getAuthority()` and
`__toString()`, as RFC 3986 section 3.2.1 requires for a `:` or `@` inside them.
Parsing accepts both a raw and an encoded value:

```php
$uri = Uri::getInstance("mongodb://us%40er:p%40ss@host");
echo $uri->getUsername(); // "us@er"
echo (string)$uri;        // "mongodb://us%40er:p%40ss@host"

// A raw "@" in the user is accepted and encoded on output
$uri = Uri::getInstance("https://email@host.com:pass@host");
echo $uri->getUsername(); // "email@host.com"
echo (string)$uri;        // "https://email%40host.com:pass@host"
```

:::note
A `%` followed by two hex digits is always read as an escape. A `%` that is not a valid escape
(e.g. `ro%&ot`) is kept as a literal `%` and is written back as `%25`.
:::

The same applies to passwords:

```php
// Creating a URI with special characters in the password
$uri = Uri::getInstance("https://user:pa&@host");
echo (string)$uri; // "https://user:pa%26@host"

// Creating a URI with already encoded characters
$uri = Uri::getInstance("https://user:pa%26@host");
echo (string)$uri; // "https://user:pa%26@host"
```

## Using withUserInfo

The `withUserInfo()` method stores the user and the password as given, without decoding them.
They are encoded on output, so pass them unencoded:

```php
// Using withUserInfo with unencoded password
$uri = Uri::getInstance("https://host")
    ->withUserInfo("user", "pa&");
echo (string)$uri; // "https://user:pa%26@host"

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
// "name=John%20Doe&email=john%40example.com"
```

The query is kept as an ordered list of key/value pairs, exactly as it was given. Only the
encoding is normalized, so the *structure* of the query always survives a round-trip:

```php
// Dots and other unreserved characters are left alone (RFC3986 section 2.3)
echo (string)Uri::getInstance("https://host/p?session.timeout.ms=6000");
// "https://host/p?session.timeout.ms=6000"

// Repeated keys are kept, in order
echo (string)Uri::getInstance("https://host/p?tag=x&tag=y");
// "https://host/p?tag=x&tag=y"

// A key with no "=" does not gain an empty value
echo (string)Uri::getInstance("https://host/p?flag");
// "https://host/p?flag"
```

:::info
Because order, repetition and encoding are preserved, a pre-signed URL (S3, CloudFront)
survives a round-trip through `Uri` with its signature intact.
:::

## How Encoding Works Internally

1. **During parsing**: The username and the password are decoded using `rawurldecode()`
2. **During output**: The username and the password in `getUserInfo()` are re-encoded with `rawurlencode()`
3. **Query strings**: Each key and value is decoded with `rawurldecode()` and re-encoded with
   `rawurlencode()`, then the pairs are joined back with `&`. A literal `+` is kept as written
   and never becomes `%2B`: form encoding reads `+` as a space and `%2B` as a plus sign, so
   converting one into the other would change the value

Normalizing this way means characters that must be encoded get encoded (a literal space
becomes `%20`), while an already-encoded unreserved character is folded back to its literal
form (`%41` becomes `A`). This ensures that URIs can be parsed and re-serialized idempotently
(parsing the output produces the same URI).

:::warning
`parse_str()` is **not** used to build the query string. It is a form-data decoder that
produces PHP variable names: it rewrites `.` and space to `_`, keeps only the last of a
repeated key, and turns `a[]` into an array — see
[php/php-src#8639](https://github.com/php/php-src/issues/8639). It is still used to build a
fallback lookup index for `getQueryPart()`, so the mangled names keep resolving.
:::
