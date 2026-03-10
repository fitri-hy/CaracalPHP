# CaracalPHP – Application Documentation

Class:

```php
Caracal\Core\Application
```

`Application` adalah inti (core container) dari CaracalPHP yang bertanggung jawab untuk melakukan bootstrap framework, memuat konfigurasi, menyediakan service container, menginisialisasi database, memuat plugin, dan menjalankan HTTP kernel.

---

## Peran Application

`Application` berfungsi sebagai pusat sistem framework dengan beberapa tanggung jawab utama:

* Core service container
* Bootstrap framework
* Host untuk plugin system
* Entry point untuk request lifecycle
* Global singleton instance

Application menjadi objek utama yang digunakan untuk mengakses service seperti config, cache, database, dan plugin manager.

---

## Singleton Instance

Application menggunakan pola Singleton sehingga hanya ada satu instance selama runtime.

Method:

```php
public static function getInstance(): self
```

Penggunaan:

```php
use Caracal\Core\Application;

$app = Application::getInstance();
```

---

## Lifecycle Inisialisasi

Saat `Application` dibuat, constructor menjalankan proses bootstrap berikut:

1. Menentukan base path project
2. Mendaftarkan autoloader
3. Memuat konfigurasi
4. Mengatur timezone
5. Mendaftarkan base services
6. Bootstrap service yang diperlukan
7. Inisialisasi plugin manager
8. Memuat plugin dari folder
9. Menjalankan plugin
10. Membuat instance Kernel

Urutan tersebut memastikan semua komponen inti siap sebelum request diproses.

---

## registerCore()

```php
protected function registerCore(): void
```

Method ini mendaftarkan autoloader dan namespace utama framework.

Namespace yang digunakan:

| Namespace    | Folder       |
| ------------ | ------------ |
| Caracal\Core | /core        |
| App\Modules  | /app/Modules |

Autoloader akan mencari class berdasarkan namespace tersebut.

Contoh struktur module:

```
app/
 └ Modules/
    └ User/
       └ UserController.php
```

---

## Service Container

Application memiliki container sederhana untuk mengelola dependency dan service.

Property container:

```php
protected array $bindings = [];
protected array $instances = [];
```

Container mendukung tiga operasi utama:

* bind
* singleton
* make

### bind

Mendaftarkan service baru.

```php
$app->bind(Service::class, function ($app) {
    return new Service();
});
```

Setiap pemanggilan `make()` akan membuat instance baru.

---

### singleton

Mendaftarkan service yang hanya dibuat sekali.

```php
$app->singleton(Logger::class, function ($app) {
    return new Logger();
});
```

Instance akan disimpan di `$instances`.

---

### make

Mengambil instance dari container.

```php
$service = $app->make(Service::class);
```

Jika service belum ada, resolver akan dipanggil.

---

## Base Services

Base services didaftarkan di method:

```php
protected function registerBaseServices()
```

Service bawaan yang tersedia:

| Service | Deskripsi           |
| ------- | ------------------- |
| cache   | Instance Cache      |
| config  | Instance Config     |
| db      | Database connection |
| plugins | Plugin manager      |

Service tersebut dapat diakses melalui method helper pada Application.

---

## Config Service

Config digunakan untuk mengakses konfigurasi aplikasi.

Method:

```php
$app->config()
```

Contoh penggunaan:

```php
$debug = $app->config()->get('app.debug');
```

Config membaca konfigurasi dari file dan environment variable.

---

## Database Service

Database hanya diinisialisasi jika konfigurasi berikut aktif:

```
db.enabled = true
```

Jika koneksi database gagal, properti `$database` akan berisi `null`.

Mengakses database:

```php
$db = $app->db();
```

Return value:

| Value             | Kondisi                              |
| ----------------- | ------------------------------------ |
| Database instance | koneksi berhasil                     |
| null              | database disabled atau gagal koneksi |

---

## Cache Service

Cache diakses melalui container dan dibuat secara lazy.

Method:

```php
$app->cache()
```

Contoh penggunaan:

```php
$app->cache()->set('key', 'value');
$value = $app->cache()->get('key');
```

Cache hanya dibuat saat pertama kali dipanggil.

---

## Plugin System

Plugin manager disimpan dalam property:

```php
protected Plugin $plugin;
```

Plugin dapat diakses melalui:

```php
$app->plugins();
```

Plugin manager bertanggung jawab untuk memuat dan menjalankan plugin framework.

---

## Folder Plugin

Plugin otomatis dimuat dari folder berikut:

```
/plugins
```

Semua file dengan ekstensi `.php` akan dimuat:

```php
$this->plugin->load($file);
```

Setelah dimuat, plugin dijalankan saat proses boot aplikasi.

---

## Boot Process

Method:

```php
protected function boot()
```

Boot process memastikan plugin hanya dijalankan sekali selama runtime.

Jika aplikasi sudah pernah boot, method ini tidak akan dijalankan lagi.

---

## Path Helper

Method:

```php
public function path(string $path = ''): string
```

Method ini menghasilkan absolute path berdasarkan root project.

Contoh:

```php
$app->path('plugins');
```

Hasil:

```
{projectRoot}/plugins
```

---

## Base Path

Method:

```php
public function basePath(): string
```

Digunakan untuk mengambil root directory dari project.

Contoh:

```php
$root = $app->basePath();
```

---

## Menjalankan Aplikasi

Method utama untuk menjalankan framework:

```php
public function run(): void
```

Method ini memanggil kernel:

```php
$this->kernel->handle();
```

Biasanya digunakan pada file entry point seperti `public/index.php`.

Contoh:

```php
use Caracal\Core\Application;

$app = Application::getInstance();
$app->run();
```

---

## Properti Utama

| Properti   | Deskripsi                   |
| ---------- | --------------------------- |
| $config    | Instance Config             |
| $kernel    | HTTP Kernel                 |
| $database  | Database connection         |
| $plugin    | Plugin manager              |
| $basePath  | Root project                |
| $bindings  | Service container bindings  |
| $instances | Singleton service instances |

---

## Service yang Tersedia

| Service  | Cara Akses        |
| -------- | ----------------- |
| Config   | `$app->config()`  |
| Database | `$app->db()`      |
| Cache    | `$app->cache()`   |
| Plugin   | `$app->plugins()` |
| Kernel   | `$app->kernel`    |

---

## Contoh Penggunaan

Mengambil konfigurasi:

```php
$app = Application::getInstance();

$env = $app->config()->get('app.env');
```

Menggunakan cache:

```php
$app->cache()->set('user', $data);
```

Menggunakan database:

```php
$db = $app->db();
```

Menggunakan service container:

```php
$app->singleton(UserService::class, function ($app) {
    return new UserService();
});

$userService = $app->make(UserService::class);
```