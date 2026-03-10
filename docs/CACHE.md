# CaracalPHP – Cache Documentation

Class

```php
Caracal\Core\Cache
```

`Cache` provides a temporary storage system used to improve application performance by storing frequently accessed data.

The cache system supports two drivers.

file
redis

The driver can be configured through environment variables.

---

## Configuration

Cache configuration can be defined in the `.env` file or configuration files.

```
CACHE_ENABLED=true
CACHE_DRIVER=file
CACHE_TTL=3600
CACHE_PREFIX=caracal:
```

If `CACHE_ENABLED=false`, all cache operations are skipped.

---

## Available Methods

The cache system provides the following methods.

```
set(string $key, mixed $value, int $ttl = null)
get(string $key, mixed $default = null)
has(string $key)
delete(string $key)
remember(string $key, callable $callback, int $ttl = null)
clearAll()
getDefaultTTL()
```

---

## Storing Cache Data

```
$cache->set('home.posts', $posts, 600);
```

TTL is defined in seconds.

---

## Retrieving Cache Data

```
$posts = $cache->get('home.posts');
```

If the cache does not exist, the method returns `null`.

---

## Checking Cache Existence

```
if ($cache->has('home.posts')) {
}
```

---

## remember()

This method is the most efficient way to use caching.

```
$posts = $cache->remember('home.posts', function() {
    return (new HomeModel())->getAllPosts();
}, 600);
```

If the cache already exists, the callback will not be executed.

---

## Deleting Cache

```
$cache->delete('home.posts');
```

---

## Clearing All Cache

```
$cache->clearAll();
```

---

## File Cache Storage

If the `file` driver is used, cache files are stored in the following directory.

```
storage/cache/
```

File format

```
{hash}.cache
```

---

## Redis Cache

If the `redis` driver is enabled, cache is stored in Redis using the following command.

```
setex(key, ttl, serialized_value)
```

If Redis is not available, the system automatically falls back to file cache.