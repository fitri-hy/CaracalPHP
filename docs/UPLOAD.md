# 📘 CaracalPHP – Upload Documentation

## Overview

`Caracal\Core\Upload` adalah **engine upload file profesional** untuk CaracalPHP yang mendukung:

* **Single & multiple file upload**
* **Validasi ukuran file** (`UPLOAD_MAX_SIZE` di `.env` atau custom)
* **Validasi ekstensi & MIME type**
* **Rename otomatis** untuk mencegah bentrok file
* **Overwrite opsional**
* Menyimpan file di **storage internal** (`Storage`)
* Mengembalikan metadata lengkap setelah upload
* Error handling lengkap

> Semua file disimpan di folder internal `storage/uploads` secara default atau bisa kustom path melalui config.

---

## Initialization

```php
use Caracal\Core\Upload;

// Inisialisasi default
$upload = new Upload();

// Custom configuration
$upload = new Upload([
    'path' => __DIR__.'/../storage/private_files',
    'max_size' => 2 * 1024 * 1024, // 2 MB
    'allowed_ext' => ['jpg','png','gif'],
    'allowed_mime' => ['image/jpeg','image/png','image/gif'],
]);
```

> Jika tidak diset, `max_size` default diambil dari `.env` `UPLOAD_MAX_SIZE` (default `5M`).

---

## Single File Upload

```php
$file = $_FILES['avatar'];

$result = $upload->save($file, 'users/avatars');

// Contoh hasil metadata:
print_r($result);
/*
[
    [
        'original_name' => 'photo.jpg',
        'stored_name'   => 'photo.jpg',
        'path'          => 'users/avatars/photo.jpg',
        'size'          => 102400,
        'extension'     => 'jpg'
    ]
]
*/
```

---

## Multiple File Upload

```php
$files = $_FILES['documents'];

$results = $upload->save($files, 'users/docs');

foreach ($results as $file) {
    echo "Uploaded: {$file['stored_name']} ({$file['size']} bytes)\n";
}
```

---

## Allowed File Types & Extensions

```php
$upload = new Upload([
    'allowed_ext' => ['jpg','png','pdf'],
    'allowed_mime' => ['image/jpeg','image/png','application/pdf']
]);

$upload->save($_FILES['file'], 'uploads'); // akan validasi tipe file
```

> Jika file tidak valid → akan melempar **Exception** dengan pesan jelas.

---

## Overwrite File

```php
$upload->save($_FILES['avatar'], 'users/avatars', true); // akan menimpa file lama
```

* Default: **tidak overwrite** → otomatis rename jika file sudah ada.
* Opsi `true` → menimpa file lama.

---

## Auto Rename

Jika file sudah ada dan overwrite **false**, nama file akan diubah otomatis:

```
photo.jpg → photo_1.jpg → photo_2.jpg ...
```

---

## Max Upload Size

* Bisa diatur lewat config saat inisialisasi (`max_size` dalam bytes)
* Default diambil dari `.env`:

```dotenv
UPLOAD_MAX_SIZE=5M
```

* Konversi otomatis: `5M` → 5 * 1024 * 1024 bytes.

---

## Error Handling

Jika upload gagal, `Upload::save()` akan melempar **Exception** dengan pesan human-readable:

* File terlalu besar
* Ekstensi tidak diperbolehkan
* MIME type tidak sesuai
* Upload partial atau gagal menulis file
* Folder target tidak tersedia

---

## Example: Full Flow

```php
$upload = new \Caracal\Core\Upload([
    'allowed_ext' => ['jpg','png','gif'],
    'allowed_mime' => ['image/jpeg','image/png','image/gif'],
]);

try {
    $result = $upload->save($_FILES['avatar'], 'users/avatars');
    echo "File uploaded: " . $result[0]['path'];
} catch (\Exception $e) {
    echo "Upload failed: " . $e->getMessage();
}
```

---

## Notes

* Default storage path: `storage/uploads`
* Bisa diganti path custom untuk **modul/plugin internal**
* Mendukung **single & multiple upload**
* Metadata lengkap dikembalikan untuk semua file
* Validasi aman dengan size, extension, dan MIME type
* Auto rename untuk menghindari konflik file
* Exception handling untuk setiap error upload