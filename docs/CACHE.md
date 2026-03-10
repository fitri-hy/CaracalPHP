# CaracalPHP Cache Usage Guide

Class:

```php
Caracal\Core\Cache
```

Cache menyediakan sistem penyimpanan sementara untuk meningkatkan performa aplikasi dengan menyimpan data yang sering diakses.

Cache mendukung dua driver:

* file
* redis

Driver dapat dikonfigurasi melalui environment.

---

## Konfigurasi

Pengaturan cache melalui `.env` atau config:

```
CACHE_ENABLED=true
CACHE_DRIVER=file
CACHE_TTL=3600
CACHE_PREFIX=caracal:
```

Jika `CACHE_ENABLED=false`, semua operasi cache akan dilewati.

---

## Method Utama

Cache menyediakan method berikut:

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

## Menyimpan Cache

```
$cache->set('home.posts', $posts, 600);
```

TTL dalam detik.

---

## Mengambil Cache

```
$posts = $cache->get('home.posts');
```

Jika cache tidak ada, return `null`.

---

## Mengecek Cache

```
if ($cache->has('home.posts')) {
}
```

---

## remember()

Method ini adalah cara paling efisien menggunakan cache.

```
$posts = $cache->remember('home.posts', function() {
    return (new HomeModel())->getAllPosts();
}, 600);
```

Jika cache tersedia, callback tidak akan dijalankan.

---

## Menghapus Cache

```
$cache->delete('home.posts');
```

---

## Menghapus Semua Cache

```
$cache->clearAll();
```

---

## File Cache Storage

Jika menggunakan driver `file`, cache disimpan di:

```
storage/cache/
```

Format file:

```
{hash}.cache
```

---

## Redis Cache

Jika driver `redis` aktif, cache disimpan di Redis menggunakan:

```
setex(key, ttl, serialized_value)
```

Jika Redis tidak tersedia, sistem otomatis fallback ke file cache.

---

## Best Practice

Gunakan pola penamaan key yang konsisten.

Contoh:

```
module.entity.action
```

Contoh nyata:

```
home.posts
home.sidebar
user.profile.12
page.home
```

Untuk halaman dinamis:

```
$key = 'page_' . md5($request->getUri());
```