# 📘 CaracalPHP – ErrorHandler Documentation

Class:

```php
Caracal\Core\ErrorHandler
```

Class ini bertugas untuk:

* Menangani semua `Throwable` (Exception & Error)
* Membersihkan output buffer
* Menampilkan halaman error HTML
* Menampilkan code snippet jika debug aktif
* Mengembalikan `Response` dengan status 500

---

# 🎯 Tujuan ErrorHandler

Method utama:

```php
public static function handle(
    Throwable $e,
    ?string $controller = null,
    ?string $method = null
): Response
```

Digunakan untuk:

* Menangani error global aplikasi
* Memberikan halaman error yang terformat
* Membantu debugging saat `app_debug = true`

---

# 1️⃣ Cara Kerja Internal

Saat `handle()` dipanggil:

1. Ambil instance `Application`
2. Ambil config `app_debug`
3. Bersihkan seluruh output buffer
4. Ambil:

   * File error
   * Line error
   * Message
   * Class exception
5. Jika controller & method diberikan:

   * Gunakan Reflection untuk cek method
   * Ambil lokasi method tersebut
6. Jika debug aktif:

   * Tampilkan potongan kode (±5 baris)
7. Return `Response` dengan status `500`

---

# 2️⃣ Debug Mode

Error snippet hanya muncul jika:

```php
$app->config()->get('app_debug', false);
```

Contoh config:

```php
return [
    'app_debug' => true
];
```

Jika `false`, maka:

* Tidak ada snippet kode
* Hanya tampil pesan error

---

# 3️⃣ Tampilan Error

Halaman error memiliki:

* Judul: **Application Error**
* Tipe Exception
* Pesan error
* File & Line
* Snippet kode (jika debug aktif)
* Note jika method controller tidak ditemukan

Favicon otomatis diambil menggunakan:

```php
$asset->url('images/favicon.ico')
```

---

# 4️⃣ Contoh Penggunaan Global

Biasanya digunakan dalam front controller atau router:

```php
use Caracal\Core\ErrorHandler;

try {
    // jalankan controller
} catch (\Throwable $e) {
    return ErrorHandler::handle($e);
}
```

---

# 5️⃣ Contoh Dengan Controller & Method

```php
return ErrorHandler::handle(
    $e,
    App\Modules\User\Controllers\UserController::class,
    'store'
);
```

Jika method tidak ada, maka akan muncul note:

```
Method Controller::method() does not exist.
```

---

# 6️⃣ Code Snippet Behavior

Jika debug aktif:

* Ambil 5 baris sebelum error
* Ambil 5 baris sesudah error
* Highlight baris error dengan background merah

Jika debug mati:

* Tidak ada snippet ditampilkan

---

# 7️⃣ Output Response

ErrorHandler selalu mengembalikan:

```php
new Response($html, 500);
```

Artinya:

* Status HTTP 500
* Content berupa HTML

---

# 📌 Ringkasan Fitur

| Fitur                   | Keterangan              |
| ----------------------- | ----------------------- |
| Handle Throwable        | Ya                      |
| Debug Mode              | Berdasarkan `app_debug` |
| Code Snippet            | Ya (debug only)         |
| Reflection Method Check | Ya                      |
| Output Buffer Clean     | Ya                      |
| Return Type             | Response (500)          |