# Uri class

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/uri/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/uri/?branch=master)
[![Build Status](https://travis-ci.org/byjg/uri.svg?branch=master)](https://travis-ci.org/byjg/uri)

An implementation of PSR-7 UriInterface

PSR-7 requires URI compliant to RFC3986. It means the URI output will be always url encoded. The same is valid to create a new instance.
The only way to store the plain password is using ->withUserInfo()

For example:

```php
$uri = \ByJG\Util\Uri::getInstanceFromString("http://user:pa&@host");
print((string)$uri); // Will print "http://user:pa%26@host"

$uri = \ByJG\Util\Uri::getInstanceFromString("http://user:pa%26@host");
print((string)$uri); // Will print "http://user:pa%26@host"

$uri = \ByJG\Util\Uri::getInstanceFromString("http://host")
    ->withUserInfo("user", "pa%26");
print((string)$uri); // Will print "http://user:pa%2526@host"
```

## Custom methods

This class is fully compliant with the PSR UriInterface (PSR-7) but implement some useful extra methods:

- getUsername()
- getPassword()
- getQueryPart($key)
- withQueryKeyValue($key, $value, $encode = true)


More information about UriInterface:
https://github.com/php-fig/http-message/blob/master/src/UriInterface.php


