# 📘 CaracalPHP Cache Usage Guide

Dokumentasi ini menjelaskan cara menggunakan `Caracal\Core\Cache` di dalam:

* Controller
* Service
* Middleware
* View

Tanpa melakukan perubahan pada file:

```id="structure"
core/Cache.php
core/Helpers.php
```

---

# 📦 1. Konsep Dasar Cache

Class:

```php
Caracal\Core\Cache
```

Menyediakan method:

```php
set(string $key, mixed $value, int $ttl = null): void
get(string $key, mixed $default = null): mixed
delete(string $key): void
clearAll(): void
getDefaultTTL(): int
```

Cache driver:

* `file` (default)
* `redis` (jika tersedia & dikonfigurasi)

Pengaturan melalui `.env` / config:

```env
CACHE_ENABLED=true
CACHE_DRIVER=file
CACHE_TTL=3600
```

Jika `CACHE_ENABLED=false`, maka semua operasi cache akan otomatis nonaktif.

---

# 🧩 2. Penggunaan di Controller

Lokasi:

```id="controller-path"
app/Modules/{ModuleName}/Controllers/
```

Contoh:

```php
<?php

namespace App\Modules\Home\Controllers;

use Caracal\Core\Controller;
use Caracal\Core\Cache;
use App\Modules\Home\Models\HomeModel;

class HomeController extends Controller
{
    public function index()
    {
        $cache = new Cache();

        $posts = $cache->get('home.posts');

        if ($posts === null) {
            $posts = (new HomeModel())->getAllPosts();
            $cache->set('home.posts', $posts, 600); // 10 menit
        }

        return $this->view('home.view', compact('posts'));
    }
}
```

📌 Cocok untuk:

* Query database berat
* API call eksternal
* Data homepage

---

# 🧠 3. Penggunaan di Service Layer (Direkomendasikan)

Lokasi:

```id="service-path"
app/Modules/{ModuleName}/Services/
```

Contoh:

```php
<?php

namespace App\Modules\Home\Services;

use Caracal\Core\Cache;
use App\Modules\Home\Models\HomeModel;

class HomeService
{
    public function getPosts()
    {
        $cache = new Cache();

        $posts = $cache->get('home.posts');

        if ($posts === null) {
            $posts = (new HomeModel())->getAllPosts();
            $cache->set('home.posts', $posts, 600);
        }

        return $posts;
    }
}
```

Controller menjadi lebih bersih:

```php
public function index()
{
    $service = new HomeService();
    $posts = $service->getPosts();

    return $this->view('home.view', compact('posts'));
}
```

📌 Best practice: letakkan cache logic di Service, bukan di Controller.

---

# 🌍 4. Full Page Cache via Middleware

Lokasi:

```id="middleware-path"
app/Modules/{ModuleName}/Middleware/
```

Contoh middleware:

```php
<?php

namespace App\Modules\Home\Middleware;

use Caracal\Core\Cache;

class HomeCacheMiddleware
{
    public function handle($request, $next)
    {
        $cache = new Cache();

        $key = 'page_' . md5($request->getUri());

        $cached = $cache->get($key);

        if ($cached !== null) {
            echo $cached;
            return;
        }

        ob_start();
        $response = $next($request);
        $content = ob_get_clean();

        $cache->set($key, $content, 300);

        echo $content;
    }
}
```

📌 Cocok untuk:

* Landing page
* Static content
* Halaman publik

---

# 🧱 5. Fragment Cache di View

Lokasi:

```id="view-path"
app/Modules/{ModuleName}/Views/
```

Contoh:

```php
<?php
use Caracal\Core\Cache;

$cache = new Cache();

$sidebar = $cache->get('home.sidebar');

if ($sidebar === null) {
    ob_start();
?>
    <div class="sidebar">
        Konten berat disini...
    </div>
<?php
    $sidebar = ob_get_clean();
    $cache->set('home.sidebar', $sidebar, 600);
}

echo $sidebar;
?>
```

📌 Cocok untuk:

* Sidebar
* Widget
* Menu dinamis
* Partial template berat

---

# 🗑 6. Menghapus Cache

Menghapus cache tertentu:

```php
$cache = new Cache();
$cache->delete('home.posts');
```

Menghapus semua cache:

```php
$cache->clearAll();
```

---

# 🔑 7. Best Practice Penamaan Key

Gunakan pola konsisten:

```
module.entity.action
```

Contoh:

```
home.posts
home.sidebar
user.profile.12
page.home
page.about
```

Untuk halaman dinamis:

```php
$key = 'page_' . md5($request->getUri());
```

---

# ⚙️ 8. Cara Kerja Internal

Jika driver:

### File

Disimpan di:

```id="storage-path"
storage/cache/
```

Format file:

```
{key}.cache
```

### Redis

Menggunakan:

```
setex(key, ttl, serialized_value)
```

Jika Redis gagal, otomatis fallback ke file.

---

# 📌 Ringkasan

| Lokasi     | Tujuan                             |
| ---------- | ---------------------------------- |
| Controller | Cache data endpoint                |
| Service    | Cache business logic (recommended) |
| Middleware | Full page caching                  |
| View       | Fragment caching                   |
| delete()   | Hapus cache tertentu               |
| clearAll() | Bersihkan semua cache              |