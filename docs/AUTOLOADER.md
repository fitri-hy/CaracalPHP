# 📘 CaracalPHP – Autoloader Documentation

Class:

```php
Caracal\Core\Autoloader
```

`Autoloader` adalah sistem **PSR-4 style namespace loader sederhana** yang digunakan untuk memuat class secara otomatis berdasarkan namespace.

---

# 🎯 Tujuan Autoloader

Autoloader bertanggung jawab untuk:

* Mendaftarkan fungsi autoload ke SPL
* Menghubungkan namespace dengan folder
* Mengubah namespace menjadi path file
* Melakukan `require` file class secara otomatis

---

# 1️⃣ Struktur Properti

```php
protected array $prefixes = [];
```

Menyimpan mapping:

```php
[
    'Namespace\\' => [
        '/path/ke/directory/'
    ]
]
```

---

# 2️⃣ Method `register()`

```php
public function register(): void
```

Digunakan untuk mendaftarkan autoloader ke PHP:

```php
spl_autoload_register([$this, 'loadClass']);
```

Biasanya dipanggil di dalam `Application::registerCore()`.

---

# 3️⃣ Method `addNamespace()`

```php
public function addNamespace(string $prefix, string $baseDir): void
```

Digunakan untuk mendaftarkan namespace ke directory.

---

## Cara Kerja Internal

1. Prefix dibersihkan dan ditambahkan `\`
2. Base directory diberi trailing slash
3. Disimpan ke `$prefixes`

---

## Contoh Penggunaan

```php
$loader = new Autoloader();
$loader->register();

$loader->addNamespace('Caracal\\Core', __DIR__);
$loader->addNamespace('App\\Modules', '/app/Modules');
```

Mapping hasilnya:

```text
Caracal\Core\ → /core/
App\Modules\ → /app/Modules/
```

---

# 4️⃣ Method `loadClass()`

```php
public function loadClass(string $class): void
```

Method ini otomatis dipanggil saat PHP tidak menemukan class.

---

## Cara Kerja Detail

Misalnya ada class:

```php
App\Modules\User\Controllers\UserController
```

### Step 1 – Cek Prefix

Dicek apakah class diawali dengan prefix yang terdaftar.

Jika prefix:

```text
App\Modules\
```

Maka lanjut.

---

### Step 2 – Ambil Relative Class

Prefix dipotong:

```text
User\Controllers\UserController
```

---

### Step 3 – Ubah ke Path

Backslash diubah menjadi slash:

```text
User/Controllers/UserController.php
```

---

### Step 4 – Gabungkan dengan BaseDir

```text
/app/Modules/User/Controllers/UserController.php
```

---

### Step 5 – Require Jika Ada

```php
require $path;
```

Jika file ditemukan, autoload berhenti.

---

# 📌 Integrasi dengan Application

Di dalam `Application::registerCore()`:

```php
$loader = new Autoloader();
$loader->register();

$loader->addNamespace('Caracal\\Core', __DIR__);
$loader->addNamespace('App\\Modules', $this->basePath . '/app/Modules');
```

Artinya framework otomatis bisa load:

* Core classes
* Module classes

Tanpa perlu `require manual`.

---

# 📌 Struktur Folder yang Direkomendasikan

```text
/app
  /Modules
    /User
      Controllers/
      Models/
      Views/

 ...
```

Namespace harus mengikuti struktur folder.