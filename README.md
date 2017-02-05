# Uri class

An implementation of Psr UriInterface

This class is fully compliant with the PSR UriInterface (PSR-7) 
plus some methods lack on the interface:

- getUsername()
- getPassword()
- getQueryPart($key)
- withQueryKeyValue($key, $value, $encode = true)

More information about UriInterface:
https://github.com/php-fig/http-message/blob/master/src/UriInterface.php


