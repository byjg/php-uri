# Changelog - Version 6.0

## Release Date
2025-11-25

## Overview
Version 6.0 is a major release that modernizes the library with updated PHP version requirements, improved documentation, and enhanced type safety. This release focuses on supporting the latest PHP versions while deprecating legacy factory methods.

---

## New Features

### PHP 8.4 and 8.5 Support
- Added support for PHP 8.4
- Added support for PHP 8.5

### Enhanced Type Safety
- Added `#[Override]` attributes to all interface implementation methods for improved type safety
- Improved null safety in Uri property assignments using null coalescing operators

### Improved Documentation
- Added comprehensive documentation structure:
  - `docs/getting-started.md` - Quick start guide
  - `docs/uri-encoding.md` - Detailed URI encoding behavior documentation
  - `docs/additional-methods.md` - Documentation for custom methods beyond PSR-7
  - `docs/psr7-methods.md` - Complete PSR-7 method reference
  - `docs/factory-methods.md` - Factory method usage guide
  - `docs/examples.md` - Practical usage examples

### Enhanced Tooling
- Added Composer scripts for easier testing and analysis:
  - `composer test` - Run PHPUnit tests
  - `composer psalm` - Run Psalm static analysis
- Integrated Psalm with GitHub Actions using SARIF reporting and CodeQL
- Separated Psalm static analysis into dedicated workflow job

### Code Quality Improvements
- Reduced Psalm error level from 4 to 3 for stricter analysis
- Removed `tests` directory from Psalm analysis scope

---

## Breaking Changes

| Area | Before (5.x) | After (6.0) | Description |
|------|-------------|------------|-------------|
| **PHP Version** | `>=8.1 <8.5` | `>=8.3 <8.6` | Dropped support for PHP 8.1 and 8.2. Minimum requirement is now PHP 8.3 |
| **PHPUnit** | `^9.6` | `^10.5\|^11.5` | Updated to PHPUnit 10.5 or 11.5 for compatibility with PHP 8.3+ |
| **Psalm** | `^6.0` | `^5.9\|^6.13` | Updated Psalm version range for better compatibility |
| **withQueryKeyValue default** | `$isEncoded = true` | `$isEncoded = false` | Changed default parameter value in `CustomUriInterface::withQueryKeyValue()` method |

---

## Deprecations

The following methods are now deprecated and will be removed in version 7.0:

- `Uri::getInstanceFromString()` - Use `Uri::getInstance()` instead
- `Uri::getInstanceFromUri()` - Use `Uri::getInstance()` instead

Both deprecated methods have been replaced by a unified `Uri::getInstance()` method that accepts either a string or UriInterface object.

---

## Bug Fixes

- Fixed URI parsing to use safer property assignments with null coalescing operators
- Improved PHPUnit configuration for better test execution
- Updated workflow configurations for better CI/CD integration

---

## Path to Upgrade from 5.x to 6.x

### 1. Update PHP Version
Ensure your environment is running PHP 8.3 or higher:
```bash
php -v  # Should show 8.3.x, 8.4.x, or 8.5.x
```

If you're on PHP 8.1 or 8.2, you'll need to upgrade your PHP installation before upgrading to version 6.0.

### 2. Update Composer Dependencies
Update your `composer.json`:
```bash
composer require byjg/uri:^6.0
composer update
```

This will automatically install compatible versions of PHPUnit and Psalm if you're using them.

### 3. Replace Deprecated Factory Methods
Search your codebase for deprecated methods and replace them:

**Old code (5.x):**
```php
use ByJG\Util\Uri;

// String-based creation
$uri = Uri::getInstanceFromString("https://example.com");

// UriInterface-based creation
$uri2 = Uri::getInstanceFromUri($existingUri);
```

**New code (6.0):**
```php
use ByJG\Util\Uri;

// Unified getInstance method handles both cases
$uri = Uri::getInstance("https://example.com");
$uri2 = Uri::getInstance($existingUri);
```

Search and replace commands:
```bash
# Find all occurrences
grep -r "getInstanceFromString\|getInstanceFromUri" ./src

# The replacement is straightforward:
# getInstanceFromString() → getInstance()
# getInstanceFromUri() → getInstance()
```

### 4. Review withQueryKeyValue Usage
If you're using `withQueryKeyValue()` and relying on the default `$isEncoded` parameter:

**Old behavior (5.x):**
```php
// $isEncoded defaulted to true
$uri->withQueryKeyValue('key', 'value');  // Treated value as already encoded
```

**New behavior (6.0):**
```php
// $isEncoded defaults to false
$uri->withQueryKeyValue('key', 'value');  // Treats value as raw (not encoded)

// If you need the old behavior, explicitly pass true:
$uri->withQueryKeyValue('key', 'value', true);
```

### 5. Update Development Dependencies (Optional)
If you use PHPUnit or Psalm in your project:

```bash
# Update PHPUnit
composer require --dev phpunit/phpunit:^10.5

# Update Psalm
composer require --dev vimeo/psalm:^6.13
```

### 6. Run Tests
After upgrading, run your test suite to ensure everything works:
```bash
vendor/bin/phpunit
```

### 7. Static Analysis (Optional)
Run Psalm to catch any type-related issues:
```bash
vendor/bin/psalm
```

---

## Testing Checklist

After upgrading, verify the following:

- [ ] PHP version is 8.3 or higher
- [ ] Composer dependencies updated successfully
- [ ] All deprecated factory methods replaced with `getInstance()`
- [ ] Review any usage of `withQueryKeyValue()` with default parameters
- [ ] All unit tests pass
- [ ] Static analysis (Psalm) passes without errors
- [ ] Application runs without errors in development
- [ ] Application runs without errors in production

---

## Additional Resources

- [Migration Guide](https://github.com/byjg/php-uri)
- [Full Documentation](docs/getting-started.md)
- [PSR-7 Specification](https://www.php-fig.org/psr/psr-7/)

---

## Support

If you encounter any issues during the upgrade, please:
- Check the [GitHub Issues](https://github.com/byjg/php-uri/issues)
- Review the [documentation](docs/)
- Open a new issue if you discover a bug

---

## Contributors

Thank you to all contributors who made this release possible!
