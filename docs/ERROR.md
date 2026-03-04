# 📘 CaracalPHP – ErrorHandler Usage Guide

## 1️⃣ Menangani Error Global (500)

Gunakan `ErrorHandler::handle()` untuk menangkap exception di aplikasi:

```php
use Caracal\Core\ErrorHandler;

try {
    // Jalankan kode atau controller
    $controller = new App\Modules\Home\Controllers\HomeController();
    $controller->index();
} catch (\Throwable $e) {
    return ErrorHandler::handle($e);
}
```

**Hasil:**

* Menampilkan halaman error HTML dengan detail exception.
* Jika `app_debug = true`, akan muncul potongan kode sekitar baris error.
* Status HTTP otomatis `500`.

---

## 2️⃣ Menambahkan Controller & Method (Optional)

Jika ingin menampilkan info method controller yang error:

```php
return ErrorHandler::handle(
    $e,
    App\Modules\User\Controllers\UserController::class,
    'store'
);
```

**Hasil:**

* Menampilkan note jika method `store()` tidak ada.
* Tetap menampilkan snippet kode jika debug aktif.

---

## 3️⃣ Menggunakan Custom 404 Page

Gunakan `ErrorHandler::notFound()` saat route tidak ditemukan:

```php
use Caracal\Core\ErrorHandler;

return ErrorHandler::notFound();
```

### Path Custom 404

Buat file:

```
app/Modules/Error/Views/404.view.php
```

Contoh isi:

```html
<h1>404 – Halaman Tidak Ditemukan</h1>
<p>Maaf, halaman yang Anda cari tidak tersedia.</p>
<a href="/">Kembali ke Beranda</a>
```

**Hasil:**

* Halaman ini akan ditampilkan jika route tidak cocok.
* Status HTTP otomatis `404`.
* Jika file tidak ada → fallback ke halaman 404 default.

---

## 4️⃣ Cara Integrasi dengan Router

Di `Router.php`, panggil:

```php
$response = $router->dispatch($request);

if ($response->status() === 404) {
    return ErrorHandler::notFound();
}
```

Dengan ini, setiap URL yang tidak terdaftar akan otomatis menampilkan halaman 404.

---

## 5️⃣ Debug Mode

Aktifkan di `.env` atau `config.php`:

```env
APP_DEBUG=true
```

**Efek:**

* Menampilkan potongan kode (±5 baris) pada halaman error 500.
* Highlight baris error dengan warna merah.
* Tidak memengaruhi 404.

---

## 6️⃣ Ringkas Cara Pakai

| Skenario                  | Cara Pakai                                              | Status HTTP |
| ------------------------- | ------------------------------------------------------- | ----------- |
| Error / Exception global  | `ErrorHandler::handle($e)`                              | 500         |
| Error controller & method | `ErrorHandler::handle($e, Controller::class, 'method')` | 500         |
| Route tidak ditemukan     | `ErrorHandler::notFound()`                              | 404         |
| Custom 404                | Buat `app/Modules/Error/Views/404.view.php`             | 404         |
| Debug mode aktif          | `.env -> APP_DEBUG=true`                                | –           |