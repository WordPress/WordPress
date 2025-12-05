# Matomo/Cache

This is a PHP caching library based on [Doctrine cache](https://github.com/doctrine/cache) that supports different backends. 
At [Matomo](https://matomo.org) we developed this library with the focus on speed as we make heavy use of caching and 
sometimes fetch hundreds of entries from the cache in one request.

[![Build Status](https://travis-ci.com/matomo-org/component-cache.svg?branch=master)](https://travis-ci.com/matomo-org/component-cache)

## Installation

With Composer:

```json
{
    "require": {
        "matomo/cache": "*"
    }
}
``` 

## Supported backends
* Array (holds cache entries only during one request but is very fast)
* Null (useful for development, won't cache anything)
* File (stores the cache entry on the file system)
* Redis (stores the cache entry on a Redis server, requires [phpredis](https://github.com/nicolasff/phpredis))
* Chained (allows to chain multiple backends to make sure it will read from a fast cache if possible)
* DefaultTimeout and KeyPrefix can be used to wrap other backend with default parameters.

Doctrine cache provides support for many more backends and adding one of those is easy. For example:
* APC
* Couchbase
* Memcache
* MongoDB
* Riak
* WinCache
* Xcache
* ZendData

Please send a pull request in case you have added one. 

## Different caches

This library comes with three different types of caches. The naming is not optimal right now.

### Lazy

This can be considered as the default cache to use in case you don't know which one to pick. The lazy cache works with 
any backend so you can decide whether you want to persist cache entries between requests or not. It does not support 
the caching of any objects. Only boolean, numbers, strings and arrays are supported. Whenever you request an entry 
from the cache it will fetch the entry from the defined backend again which can cause many reads depending on your 
application.

### Eager

This cache stores all its cache entries under one "cache" entry in a configurable backend.

This comes handy for things that you need very often, nearly in every request. Instead of having to read eg.
a hundred cache entries from files it only loads one cache entry which contains the hundred keys. Should be used only 
for things that you need very often and only for cache entries that are not too large to keep loading and parsing the 
single cache entry fast. This cache is even more useful in case you are using a slow backend such as a file or a database.
 Instead of having a hundred stat calls there will be only one. All cache entries it contains have the same life time. 
 For fast performance it won't validate any cache ids. It is not possible to cache any objects using this cache.

### Transient

This class is used to cache any data during one request. It won't be persisted.

All cache entries will be cached in a simple array meaning it is very fast to save and fetch cache entries. You can 
basically achieve the same by using a lazy cache and a backend that does not persist any data such as the array cache 
but this one will be a bit faster as it won't validate any cache ids and it allows you to cache any kind of objects.
Compared to the lazy cache it does not support setting any life time as it will be only valid during one request anyway.
Use this one if you read hundreds or thousands of cache entries and if performance really matters to you.

## Usage

### Creating a file backend

```php
$options = array('directory' => '/path/to/cache');
$factory = new \Matomo\Cache\Backend\Factory();
$backend = $factory->buildBackend('file', $options);
```

### Creating a Redis backend

```php
$options = array(
    'host'     => '127.0.0.1',
    'port'     => '6379',
    'timeout'  => 0.0,
    'database' => 15, // optional
    'password' => 'secret', // optional
);
$factory = new \Matomo\Cache\Backend\Factory();
$backend = $factory->buildBackend('redis', $options);
```

### Creating a chained backend

```php
$options = array(
    'backends' => array('array', 'file'),
    'file'     => array('directory' => '/path/to/cache')
);
$factory = new \Matomo\Cache\Backend\Factory();
$backend = $factory->buildBackend('redis', $options);
```

Whenever you set a cache entry it will save it in the array and in the file cache. Whenever you are trying to read a cache
entry it will first try to get it from the fast array cache. In case it is not available there it will try to fetch
the cache entry from the file system. If the cache entry exists on the file system it will cache the entry automatically
using the array cache so the next read within this request will be fast and won't cause a stat call again. If you delete
 a cache entry it will be removed from all configured backends. You can chain any backends. It is recommended to list 
 faster backends first.
 
### Creating a decorated backend with `DefaultTimeout` or `KeyPrefix`

```php
$options = array(
    'backend'   => 'array',
    'array'     => array(),
    'keyPrefix' => 'someKeyPrefixStr'
);
$factory = new \Matomo\Cache\Backend\Factory();
$backend = $factory->buildBackend('keyPrefix', $options);
```

Sometimes its useful to set default a default parameter for the timeout to force prevent infinite lifetimes, or to
specify a key prefix to prevent different versions of the app from reading and writing the same cache entry.  
`DefaultTimeout` and `KeyPrefix` are "decorators" that wrap another backend. Currently each takes a single configuration
 argument with the same name as the backend.
 
 |backend | argument | description |
 | --- | --- | --- |
 |DefaultTimeout|`defaultTimeout`| Uses the `integer` value as the default timeout for defining cache entry lifetime. Only comes to effect when no lifetime is specified; Prevents infinite lifetimes.|
 |KeyPrefix|`keyPrefix`| Prefixes the given value to the keys for any operation. For example `'Key123'` would become `'SomePrefixKey123'` |


### Creating a lazy cache

[Description lazy cache.](#lazy)

```php
$factory = new \Matomo\Cache\Backend\Factory();
$backend = $factory->buildBackend('file', array('directory' => '/path/to/cache'));

$cache = new \Matomo\Cache\Lazy($backend);
$cache->fetch('myid');
$cache->contains('myid');
$cache->delete('myid');
$cache->save('myid', 'myvalue', $lifeTimeInSeconds = 300);
$cache->flushAll();
```

### Creating an eager cache

[Description eager cache.](#eager)

```php
$cache = new \Matomo\Cache\Eager($backend, $storageId = 'eagercache');
$cache->fetch('myid');
$cache->contains('myid');
$cache->delete('myid');
$cache->save('myid', new \stdClass());
$cache->persistCacheIfNeeded($lifeTimeInSeconds = 300);
$cache->flushAll();
```

It will cache all set cache entries under the cache entry `eagercache`.

### Creating a transient cache

[Description transient cache.](#transient)

```php
$cache = new \Matomo\Cache\Transient();
$cache->fetch('myid');
$cache->contains('myid');
$cache->delete('myid');
$cache->save('myid', new \stdClass());
$cache->flushAll();
```

## License

The Cache component is released under the [LGPL v3.0](http://choosealicense.com/licenses/lgpl-3.0/).

## Changelog 

 * 0.2.5: updating to doctrine/cache 1.4 which contains our fix
 * 0.2.4: do not throw exception when clearing a file cache if the cache dir doesn't exist
 * 0.2.3: fixed another race condition in file cache
 * 0.2.2: fixed a race condition in file cache
 * 0.2.0: Initial release
