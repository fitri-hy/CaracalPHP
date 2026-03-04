# 📘 CaracalPHP – Module Documentation

Class:

```php
Caracal\Core\Module
```

`Module` adalah registry sederhana untuk menyimpan daftar module yang tersedia dalam aplikasi.

Class ini hanya bertanggung jawab untuk:

* Mendaftarkan module
* Menyimpan path module
* Mengembalikan daftar module

Tidak ada auto-loading atau auto-discovery di dalam class ini.

---

# 🎯 Tujuan Module

* Mengelola daftar module secara terpusat
* Memastikan path module valid
* Menyediakan akses ke seluruh module terdaftar

---

# 1️⃣ Properti

```php
protected array $modules = [];
```

Struktur data:

```php
[
    'ModuleName' => '/absolute/path/to/module'
]
```

---

# 2️⃣ Method register()

```php
public function register(string $name, string $path): void
```

Digunakan untuk mendaftarkan module baru.

---

## Validasi Internal

```php
if (!is_dir($path)) {
    throw new \Exception("Module path {$path} not found");
}
```

Artinya:

* Path harus berupa direktori
* Jika tidak ada → akan melempar Exception
* Tidak ada silent fail

---

## Contoh Penggunaan

```php
use Caracal\Core\Module;

$module = new Module();

$module->register(
    'User',
    __DIR__ . '/app/Modules/User'
);
```

Jika folder tidak ada:

```text
Exception: Module path /app/Modules/User not found
```

---

# 3️⃣ Method all()

```php
public function all(): array
```

Mengembalikan seluruh module yang sudah didaftarkan.

---

## Contoh

```php
$modules = $module->all();

print_r($modules);
```

Output:

```php
[
    'User' => '/app/Modules/User',
    'Blog' => '/app/Modules/Blog'
]
```