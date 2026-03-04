# 📘 CaracalPHP – Application Documentation

Class:

```php
Caracal\Core\Application
```

`Application` adalah **inti (core container) framework** yang bertanggung jawab untuk:

* Inisialisasi sistem
* Load config
* Bootstrap service (Database)
* Load & jalankan plugin
* Menjalankan Kernel
* Menyediakan akses global (singleton)

---

# 🎯 Peran Application

Application bertindak sebagai:

* Service container sederhana
* Singleton global instance
* Bootstrapper seluruh framework

---

# 1️⃣ Singleton Pattern

Application menggunakan singleton:

```php
public static function getInstance(): self
```

Artinya hanya ada satu instance selama runtime.

---

## Cara Mengambil Instance

```php
use Caracal\Core\Application;

$app = Application::getInstance();
```

---

# 2️⃣ Urutan Inisialisasi Internal

Saat `Application` dibuat, constructor akan:

```php
private function __construct()
```

Urutan eksekusi:

1. Set `$basePath`
2. Register autoloader
3. Load Config
4. Bootstrap services (Database)
5. Inisialisasi Plugin manager
6. Load plugin dari folder
7. Jalankan plugin
8. Buat Kernel

---

# 3️⃣ registerCore()

```php
protected function registerCore(): void
```

Mendaftarkan namespace:

```php
Caracal\Core
App\Modules
```

Autoload akan mencari module di:

```text
/app/Modules
```

---

# 4️⃣ Config Access

```php
public function config(): Config
```

Contoh penggunaan:

```php
$app = Application::getInstance();

$debug = $app->config()->get('app_debug');
```

---

# 5️⃣ Database Bootstrap

Database hanya diinisialisasi jika:

```php
$this->config->get('db.enabled', true)
```

Jika koneksi gagal:

```php
$this->database = null;
```

---

## Mengakses Database

```php
$app->db();
```

Return:

* Instance `Database`
* Atau `null` jika disabled/gagal koneksi

---

# 6️⃣ Cache Service

```php
public function cache(): Cache
```

Cache dibuat secara lazy-loading:

```php
if ($this->cache === null) {
    $this->cache = new Cache();
}
```

Contoh penggunaan:

```php
$app->cache()->set('key', 'value');
```

---

# 7️⃣ Plugin System

Plugin manager disimpan di:

```php
protected Plugin $plugin;
```

---

## Mengambil Plugin Manager

```php
$app->plugins();
```

---

## Cara Plugin Diload

Folder yang dibaca:

```text
/plugins
```

Semua file `.php` di folder tersebut akan:

```php
$this->plugin->load($file);
```

Kemudian dijalankan:

```php
$this->plugin->run();
```

---

# 8️⃣ Path Helper

```php
public function path(string $path = ''): string
```

Menghasilkan absolute path berdasarkan basePath.

Contoh:

```php
$app->path('plugins');
```

Hasil:

```text
{basePath}/plugins
```

---

# 9️⃣ Menjalankan Aplikasi

```php
public function run(): void
```

Method ini hanya memanggil:

```php
$this->kernel->handle();
```

Biasanya digunakan di `public/index.php`:

```php
use Caracal\Core\Application;

$app = Application::getInstance();
$app->run();
```

---

# 📌 Properti Utama

| Properti  | Fungsi                      |
| --------- | --------------------------- |
| $config   | Instance Config             |
| $kernel   | Instance Kernel             |
| $database | Instance Database atau null |
| $cache    | Lazy Cache instance         |
| $plugin   | Plugin manager              |
| $basePath | Root project path           |

---

# 📌 Service yang Dikelola

| Service  | Cara Akses        |
| -------- | ----------------- |
| Config   | `$app->config()`  |
| Database | `$app->db()`      |
| Cache    | `$app->cache()`   |
| Plugin   | `$app->plugins()` |
| Kernel   | `$app->kernel`    |