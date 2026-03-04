# 📘 CaracalPHP – Sanitizer Usage Documentation

`Caracal\Core\Sanitizer` digunakan untuk membersihkan input HTML agar aman dari serangan seperti **XSS (Cross-Site Scripting)**.

Sanitizer menggunakan **HTMLPurifier** untuk memastikan output aman tanpa merusak struktur HTML yang valid.

---

# 1. Class Location

```php
Caracal\Core\Sanitizer
```

---

# 2. Tujuan Sanitizer

Sanitizer digunakan untuk:

* Membersihkan input dari user
* Mengamankan rich text editor
* Mengizinkan iframe hanya dari domain tertentu
* Membersihkan array input (misalnya $_POST)

---

# 3. Basic Usage

## Membersihkan String

```php
use Caracal\Core\Sanitizer;

$sanitizer = new Sanitizer();

$clean = $sanitizer->clean($input);
```

Metode:

```php
public function clean(string $input): string
```

Fungsi ini akan:

* Menghapus script berbahaya
* Menghapus atribut berbahaya
* Memastikan HTML aman

---

# 4. Membersihkan Array Input

Jika Anda memiliki input dalam bentuk array:

```php
$cleanData = $sanitizer->cleanArray($_POST);
```

Metode:

```php
public function cleanArray(array $data): array
```

Behavior:

* Setiap value string akan dibersihkan
* Value non-string akan dibiarkan
* Tidak melakukan recursive nested array (sesuai implementasi saat ini)

Contoh:

Input:

```php
[
    'name' => '<script>alert(1)</script>',
    'age' => 25
]
```

Output:

```php
[
    'name' => '',
    'age' => 25
]
```

---

# 5. Mengizinkan Iframe Domain Tertentu

Secara default iframe dibatasi.
Anda dapat mengizinkan domain tertentu melalui constructor.

```php
$sanitizer = new Sanitizer([
    'www.youtube.com',
    'youtube.com'
]);
```

Sanitizer akan mengizinkan iframe hanya dari domain tersebut.

Contoh input:

```html
<iframe src="https://www.youtube.com/embed/xxxx"></iframe>
```

Jika domain tidak diizinkan → iframe akan dihapus.

---

# 6. Constructor Signature

```php
public function __construct(array $allowedIframeDomains = [])
```

Parameter:

| Parameter            | Tipe  | Deskripsi                           |
| -------------------- | ----- | ----------------------------------- |
| allowedIframeDomains | array | Daftar domain iframe yang diizinkan |

Jika kosong → iframe tetap dalam mode safe, tetapi tanpa whitelist domain tambahan.

---

# 7. Contoh Penggunaan di Controller

```php
use Caracal\Core\Sanitizer;

class PostController
{
    public function store()
    {
        $sanitizer = new Sanitizer([
            'www.youtube.com'
        ]);

        $data = $sanitizer->cleanArray($_POST);

        // Simpan ke database
    }
}
```

---

# 8. Kapan Menggunakan Sanitizer

Gunakan ketika:

* Menyimpan komentar user
* Menyimpan artikel blog
* Menyimpan konten dari WYSIWYG editor
* Menerima HTML dari form

Tidak perlu digunakan untuk:

* Data numerik
* Data internal sistem
* Data yang tidak berasal dari user

---

# 10. Ringkasan Fitur

| Feature                  | Status |
| ------------------------ | ------ |
| HTML Cleaning            | ✅      |
| Script Removal           | ✅      |
| Safe Iframe Mode         | ✅      |
| Domain Whitelist         | ✅      |
| Array Cleaning           | ✅      |
| HTMLPurifier Integration | ✅      |