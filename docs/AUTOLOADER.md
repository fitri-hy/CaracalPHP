# CaracalPHP – Autoloader Documentation

Class:

```php
Caracal\Core\Autoloader
```

`Autoloader` adalah sistem pemuatan class otomatis berbasis **PSR-4 style namespace mapping** yang digunakan oleh CaracalPHP.

Autoloader ini bertanggung jawab untuk memuat file class tanpa perlu `require` manual.

---

## Tujuan Autoloader

Autoloader memiliki beberapa tanggung jawab utama:

* Mendaftarkan autoload handler ke SPL
* Menghubungkan namespace dengan directory
* Mengubah namespace menjadi path file
* Memuat class secara otomatis
* Mendukung class map dan fallback directory

---

## Struktur Properti

```php
protected array $prefixes = [];
protected array $classMap = [];
protected array $fallbackDirs = [];
```

Penjelasan:

| Properti     | Fungsi                                     |
| ------------ | ------------------------------------------ |
| prefixes     | Mapping namespace ke folder                |
| classMap     | Mapping class langsung ke file             |
| fallbackDirs | Directory fallback jika prefix tidak cocok |

---

## register()

Method:

```php
public function register(): void
```

Method ini mendaftarkan autoloader ke sistem SPL PHP.

```php
spl_autoload_register([$this, 'loadClass'], true, true);
```

Setelah method ini dipanggil, PHP akan otomatis memanggil `loadClass()` ketika class tidak ditemukan.

Biasanya dipanggil saat bootstrap framework.

---

## addNamespace()

Method:

```php
public function addNamespace(string $prefix, string $baseDir): void
```

Digunakan untuk mendaftarkan namespace ke directory.

Contoh:

```php
$loader->addNamespace('Caracal\\Core', '/core');
$loader->addNamespace('App\\Modules', '/app/Modules');
```

Mapping yang dihasilkan:

```
Caracal\Core\ → /core/
App\Modules\ → /app/Modules/
```

---

## addFallbackDir()

Method:

```php
public function addFallbackDir(string $dir): void
```

Menambahkan directory fallback yang akan digunakan jika namespace tidak cocok dengan prefix yang terdaftar.

Contoh:

```php
$loader->addFallbackDir('/lib');
```

Jika class tidak ditemukan di prefix mapping, autoloader akan mencari di folder fallback.

---

## addClassMap()

Method:

```php
public function addClassMap(array $map): void
```

Digunakan untuk menambahkan mapping class langsung ke file.

Contoh:

```php
$loader->addClassMap([
    'App\\Helpers\\Str' => '/app/Helpers/Str.php'
]);
```

Keuntungan:

* Lebih cepat
* Tidak perlu scanning namespace

---

## loadClass()

Method utama autoloader:

```php
public function loadClass(string $class): bool
```

Method ini dipanggil otomatis oleh PHP saat class tidak ditemukan.

Langkah kerja autoloader:

1. Cek apakah class ada di `classMap`
2. Cek prefix namespace yang cocok
3. Konversi namespace menjadi path
4. Gabungkan dengan base directory
5. Require file jika ditemukan
6. Jika tidak ditemukan, cek fallback directory

---

## Contoh Proses Autoload

Jika ada class:

```
App\Modules\User\Controllers\UserController
```

Dan namespace mapping:

```
App\Modules\ → /app/Modules/
```

Langkah yang dilakukan autoloader:

Relative class:

```
User\Controllers\UserController
```

Konversi menjadi path:

```
User/Controllers/UserController.php
```

Gabungkan dengan base directory:

```
/app/Modules/User/Controllers/UserController.php
```

Jika file ada, autoloader akan menjalankan:

```php
require $file;
```

---

## Integrasi dengan Application

Autoloader biasanya diinisialisasi di dalam `Application`.

Contoh:

```php
$loader = new Autoloader();
$loader->register();

$loader->addNamespace('Caracal\\Core', __DIR__);
$loader->addNamespace('App\\Modules', $app->path('app/Modules'));
```

Dengan konfigurasi ini, framework dapat memuat:

* Core class
* Module class

tanpa require manual.

---

## Struktur Folder yang Direkomendasikan

```
app/
 └ Modules/
    └ User/
       ├ Controllers/
       ├ Models/
       └ Views/
```

Namespace harus mengikuti struktur folder.

Contoh:

```
namespace App\Modules\User\Controllers;
```