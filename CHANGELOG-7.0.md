# Changelog - Version 7.0

> **Status: in development.** This document tracks changes landing on the `7.0` branch.
> Nothing here is released yet, and the contents may still change.

## Bug Fixes

- **The query string is no longer corrupted on output** ([#23](https://github.com/byjg/php-uri/issues/23)).

  The query used to be stored only as a `parse_str()` array and rebuilt with
  `http_build_query()`, so no query string survived a round-trip:

  | Input | Before | Now |
  |---|---|---|
  | `?group.id=g1` | `?group_id=g1` | `?group.id=g1` |
  | `?tag=x&tag=y` | `?tag=y` | `?tag=x&tag=y` |
  | `?a[]=1&a[]=2` | `?a%5B0%5D=1&a%5B1%5D=2` | `?a%5B%5D=1&a%5B%5D=2` |
  | `?my%20key=v` | `?my_key=v` | `?my%20key=v` |
  | `?flag` | `?flag=` | `?flag` |

  `parse_str()` is a form-data decoder that produces PHP variable names, not a URI query
  parser — it rewrites `.` and space to `_`, keeps only the last of a repeated key, and
  turns `a[]` into an array (see [php/php-src#8639](https://github.com/php/php-src/issues/8639),
  which is closed as stale and needs an RFC, so this will not change upstream).

  The query is now kept as an ordered list of raw key/value pairs and only its *encoding*
  is normalized on output, which keeps the library RFC3986-compliant and satisfies the PSR-7
  requirement that `withQuery($query)->getQuery()` returns `$query`. As a side effect,
  pre-signed URLs (S3, CloudFront) now survive a round-trip with their signature intact.

- `getQueryPart()` now resolves keys exactly as they were written, so
  `getQueryPart('group.id')` returns the value instead of `null`. Its signature is unchanged
  (`?string`, the last value when a key repeats), so no existing caller needs to be touched.

- `getQueryPart()` no longer throws a `TypeError` for a bracket key such as `?a[]=1`
  (`parse_str()` produced an array where a `?string` was declared).

- **A one-character hostname with a port now parses** ([#25](https://github.com/byjg/php-uri/issues/25)).

  `kafka://h:9092` returned an empty host and a null port, and swallowed the whole authority
  into the path. Two or more characters always worked, so only the one-character case failed:

  | Input | Before | Now |
  |---|---|---|
  | `kafka://h:9092` | host `''`, port `null`, path `h:9092` | host `h`, port `9092` |
  | `kafka://a:9092/path` | host `''`, port `null`, path `a:9092/path` | host `a`, port `9092`, path `/path` |
  | `mysql://user:pw@h:3306/db` | host `''`, port `null`, path `h:3306/db` | host `h`, port `3306`, path `/db` |

  The host pattern guards against reading a Windows drive letter as a host, and the guard
  `(?![A-Za-z]:)` rejected *any* letter before a colon -- a one-character host looked exactly
  like `C:`. The guard is now `(?![A-Za-z]:(?!\d))`: a letter and a colon are only a drive
  letter when what follows is **not** a port. Windows paths, including the drive-relative
  `C:foo` and `C:`, are unaffected.

  Deliberate trade-off: `C:1` is now read as host `C` on port 1 rather than as a
  drive-relative path. The two readings are indistinguishable; write `C:\1` or `C:/1` for the
  path. Documented in `docs/examples.md`.

## New Features

- `CustomUriInterface::getQueryParts(string $key): array` returns **every** value of a key,
  in order — the only way to read a repeated key, now that `?tag=x&tag=y` survives:

  ```php
  $uri = Uri::getInstance('https://example.com?tag=x&page=1&tag=y');

  $uri->getQueryParts('tag');     // ["x", "y"]
  $uri->getQueryParts('page');    // ["1"]
  $uri->getQueryParts('missing'); // []
  ```

  `getQueryPart()` is left alone and still returns `?string`, so the ~28 call sites across
  `php-anydataset-db`, `php-rabbitmq-client`, `php-migration` and `php-mailwrapper` — all of
  which read single-valued DSN options — keep working untouched.

## Deprecations

- **The `parse_str()` mangled-name fallback in `getQueryPart()` and `hasQueryKey()` is
  deprecated and will be removed in 8.0.** Now that keys resolve as written,
  `getQueryPart('group_id')` for a `?group.id=1` query is only still supported so that code
  written against the old broken behaviour keeps running. Ask for the real key instead.
  `getQueryParts()` never consults this index.

  No runtime deprecation is emitted; this is a documentation-only notice.

## Breaking Changes

- None for well-formed input; `getQuery()` and `__toString()` now return the query the way
  it was given, so any code asserting on the mangled output above must be updated.

## Requirements

- PHP 8.3, 8.4, 8.5 and 8.6 are now supported: `"php": ">=8.3 <8.7"`.
  The previous `<8.6` upper bound excluded PHP 8.6, since `<8.6` is exclusive.

## Toolchain

- PHPUnit updated to `^12.5`.
- Psalm updated to `^6.16`.

  PHPUnit 13 is deliberately **not** used. It requires PHP `>=8.4.1`, which would
  break the 8.3 floor, and it pulls `sebastian/diff ^9.0`, which the newest stable
  Psalm (6.16.1) does not accept — that combination silently resolves Psalm to an
  unreleased `6.x-dev` branch. Pinning PHPUnit to `^12.5` keeps a single stable
  PHPUnit and a single stable Psalm across the whole matrix.

## Continuous Integration

- The build matrix now includes PHP 8.6.
- The Psalm job now runs on PHP 8.5. Psalm 6.16.1 declares
  `~8.1.31 || ~8.2.27 || ~8.3.16 || ~8.4.3 || ~8.5.0` and therefore cannot be
  installed on PHP 8.6.

## Housekeeping

- `phpunit.xml.dist` renamed to `phpunit.xml`.
