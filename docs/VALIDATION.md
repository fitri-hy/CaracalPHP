# 📘 CaracalPHP – Validation Documentation

## Overview

`Caracal\Core\Validation` adalah **wrapper sederhana** untuk library [Respect\Validation](https://respect-validation.readthedocs.io/) yang digunakan untuk **memvalidasi data input** dengan mudah dan rapi.

Fitur utama:

* Validasi array data dengan **rules per field**
* Menangkap semua **error messages** dalam format array
* Integrasi mudah untuk request forms, API input, dan modul CaracalPHP

> ⚡ Mempermudah validasi tanpa menulis banyak boilerplate.

---

## Initialization

```php
use Caracal\Core\Validation;

$validator = new Validation();
```

Tidak perlu konfigurasi tambahan, cukup instantiate object.

---

## Basic Usage

### Define Rules

Gunakan class `Respect\Validation\Validator` untuk mendefinisikan rules:

```php
use Respect\Validation\Validator as v;

$rules = [
    'name'  => v::stringType()->notEmpty()->length(3, 50),
    'email' => v::email()->notEmpty(),
    'age'   => v::optional(v::intType()->min(18))
];
```

### Validate Data

```php
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25
];

if ($validator->validate($data, $rules)) {
    echo "Data valid!";
} else {
    print_r($validator->errors());
}
```

Jika validasi gagal, `errors()` akan mengembalikan array:

```php
[
    'name' => [
        "Name must have a length between 3 and 50"
    ],
    'email' => [
        "Email must be valid"
    ]
]
```

---

## Handling Optional Fields

Gunakan `v::optional()` untuk fields yang **boleh kosong**:

```php
$rules = [
    'phone' => v::optional(v::phone())
];
```

Jika `phone` kosong, validasi akan dilewati.

---

## Advanced Example

Validasi form pendaftaran:

```php
$data = [
    'username' => 'johndoe',
    'password' => 'secret123',
    'email'    => 'invalid-email'
];

$rules = [
    'username' => v::alnum()->noWhitespace()->length(3, 20),
    'password' => v::stringType()->length(6, 50),
    'email'    => v::email()
];

if (!$validator->validate($data, $rules)) {
    foreach ($validator->errors() as $field => $messages) {
        foreach ($messages as $msg) {
            echo "<p>$field error: $msg</p>";
        }
    }
}
```

> ✅ Semua error dikumpulkan sekaligus, sehingga bisa ditampilkan di form atau dikembalikan ke API.

---

## Notes

* `Validation` adalah **lightweight wrapper** – menggunakan Respect\Validation di bawahnya.
* Error messages otomatis menggunakan **field name capitalized**
* Cocok untuk **web forms**, **API request**, atau **module input validation**
* Mendukung semua validator Respect\Validation standar seperti:

  * `v::email()`, `v::intType()`, `v::length()`, `v::alnum()`, `v::phone()`, dll.