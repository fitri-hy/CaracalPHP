# 📘 CaracalPHP – Exception System Documentation

File ini mendefinisikan custom exception untuk framework:

```php
Caracal\Core\CaracalException
Caracal\Core\NotFoundException
Caracal\Core\ValidationException
Caracal\Core\CSRFException
```

Semua exception turunan berasal dari:

```php
Caracal\Core\CaracalException
```

---

# 1️⃣ CaracalException

Class utama:

```php
class CaracalException extends \Exception
```

Fitur tambahan dibanding Exception biasa:

* Mendukung `context` (array data tambahan)
* Memiliki method `toArray()`

---

## Constructor

```php
public function __construct(
    string $message = "",
    int $code = 0,
    array $context = [],
    Throwable $previous = null
)
```

Parameter tambahan dibanding Exception standar:

| Parameter   | Fungsi                          |
| ----------- | ------------------------------- |
| `$context`  | Data tambahan terkait error     |
| `$previous` | Exception sebelumnya (chaining) |

---

## Contoh Penggunaan

```php
use Caracal\Core\CaracalException;

throw new CaracalException(
    'Something went wrong',
    500,
    ['user_id' => 10]
);
```

---

# 2️⃣ Mengambil Context

Gunakan:

```php
$e->getContext();
```

Contoh:

```php
try {
    throw new CaracalException('Error', 500, ['id' => 1]);
} catch (CaracalException $e) {
    $context = $e->getContext();
}
```

---

# 3️⃣ Konversi ke Array

Method:

```php
$e->toArray();
```

Return:

```php
[
    'message' => string,
    'code'    => int,
    'file'    => string,
    'line'    => int,
    'context' => array,
    'trace'   => string,
]
```

Contoh:

```php
try {
    throw new CaracalException('Database error', 500, ['table' => 'users']);
} catch (CaracalException $e) {
    print_r($e->toArray());
}
```

Cocok untuk:

* JSON API response
* Logging
* Debugging

---

# 4️⃣ Exception Turunan

## 🔹 NotFoundException

```php
class NotFoundException extends CaracalException {}
```

Digunakan untuk:

* Route tidak ditemukan
* Resource tidak ditemukan
* Data tidak ada

Contoh:

```php
use Caracal\Core\NotFoundException;

throw new NotFoundException('User not found');
```

---

## 🔹 ValidationException

```php
class ValidationException extends CaracalException {}
```

Digunakan untuk:

* Validasi form gagal
* Input tidak sesuai aturan

Contoh:

```php
use Caracal\Core\ValidationException;

throw new ValidationException(
    'Validation failed',
    422,
    ['field' => 'email']
);
```

---

## 🔹 CSRFException

```php
class CSRFException extends CaracalException {}
```

Digunakan untuk:

* CSRF token tidak valid
* CSRF token tidak ada

Contoh:

```php
use Caracal\Core\CSRFException;

throw new CSRFException('Invalid CSRF token');
```

---

# 📌 Perilaku Penting Sesuai Implementasi

✔ Semua turunan mewarisi `context`
✔ `toArray()` selalu menyertakan trace sebagai string
✔ `context` default adalah array kosong
✔ Tetap kompatibel dengan Throwable

---

# 📌 Perbedaan Dengan Exception Biasa

| Fitur       | Exception | CaracalException |
| ----------- | --------- | ---------------- |
| Message     | ✔         | ✔                |
| Code        | ✔         | ✔                |
| File & Line | ✔         | ✔                |
| Context     | ✖         | ✔                |
| toArray()   | ✖         | ✔                |