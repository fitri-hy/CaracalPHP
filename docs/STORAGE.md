# 📘 CaracalPHP – Storage Documentation

## Overview

`Caracal\Core\Storage` adalah **abstraksi penyimpanan file privat** untuk CaracalPHP.
Dirancang untuk menyimpan **file internal modul**, **upload**, atau konten yang tidak langsung diakses publik.

**Fitur utama:**

* Membaca dan menulis file dengan path terkelola
* Mengecek keberadaan file
* Menghapus file
* Membuat direktori otomatis
* Mendukung base path kustom
* Mendukung **driver storage** dari `.env` (`local` saat ini)
* Membatasi **ukuran upload** sesuai `UPLOAD_MAX_SIZE`

> Default base path: `storage/uploads`
> Default driver: `local`
> Default upload max size: `5M`

---

## Initialization

### Default Storage Path

```php
use Caracal\Core\Storage;

$storage = new Storage();
echo $storage->path('example.txt'); 
// C:\path_to_project\storage\uploads/example.txt
```

### Custom Storage Path

```php
$storage = new Storage(__DIR__.'/../storage/private_files');
```

> Storage otomatis membuat direktori jika belum ada.

---

## Storage Driver & Max Upload

Driver dan batas ukuran otomatis dibaca dari `.env`:

```env
FILESYSTEM_DRIVER=local
UPLOAD_MAX_SIZE=5M
```

```php
$storage = new Storage();
```

* `$storage` akan menggunakan **driver lokal**.
* File yang melebihi `UPLOAD_MAX_SIZE` akan **menghasilkan exception** saat `put()`.

---

## Basic File Operations

### Put / Write File

```php
$storage->put('docs/readme.txt', 'Hello CaracalPHP!');
```

* Membuat file `readme.txt` di `storage/uploads/docs/`
* Direktori dibuat otomatis jika belum ada
* Memeriksa ukuran file sesuai `UPLOAD_MAX_SIZE`
* Mengembalikan `true` jika berhasil

---

### Get / Read File

```php
$content = $storage->get('docs/readme.txt');
echo $content; // Hello CaracalPHP!
```

* Jika file tidak ada → mengembalikan `null`

---

### Delete File

```php
$storage->delete('docs/readme.txt');
```

* Menghapus file
* Mengembalikan `true` jika file ada dan berhasil dihapus
* `false` jika file tidak ada

---

### Check File Existence

```php
if ($storage->exists('docs/readme.txt')) {
    echo "File exists!";
}
```

---

### Get Full Path

```php
$fullPath = $storage->path('docs/readme.txt');
echo $fullPath; 
// C:\path_to_project\storage\uploads/docs/readme.txt
```

---

### Create Directory

```php
$storage->makeDir('images/users');
```

* Membuat direktori baru di bawah **base path**
* Mengembalikan `true` jika berhasil atau sudah ada

---

## Example: Module File Storage

```php
$storage = new \Caracal\Core\Storage();

// Simpan avatar user
$storage->put('users/123/avatar.png', $imageContent);

// Cek apakah file ada
if ($storage->exists('users/123/avatar.png')) {
    echo "Avatar siap diakses melalui backend!";
}

// Ambil konten
$content = $storage->get('users/123/avatar.png');

// Hapus file
$storage->delete('users/123/avatar.png');
```

---

## Notes

* Semua operasi **privat**, tidak untuk publik (gunakan folder `public/uploads` untuk user uploads publik)
* Path internal dikelola **relatif terhadap base path**
* Otomatis membuat folder yang hilang saat `put()` atau `makeDir()`
* Bisa digunakan oleh **modul** atau **plugin** untuk penyimpanan internal
* **Driver** dan **upload max size** dikonfigurasi melalui `.env` (`FILESYSTEM_DRIVER` & `UPLOAD_MAX_SIZE`)
* Saat menggunakan driver selain `local`, class akan menolak jika belum diimplementasikan