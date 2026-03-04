# 📘 CaracalPHP – Session Documentation

## Overview

`Caracal\Core\Session` adalah **engine session aman** untuk CaracalPHP yang mendukung:

* Penyimpanan data session standar (`get`, `set`, `remove`)
* **Flash data** (satu kali tampil)
* **Regenerasi session ID** untuk keamanan
* **Enkripsi & HMAC Signing** menggunakan `APP_KEY`
* Multi-driver session:

  * **File** (default, path: `storage/sessions`)
  * **Redis** (menggunakan Predis)
  * **Database** (tabel `sessions` otomatis dibuat jika belum ada)

> 🔐 Semua data session dienkripsi dengan AES-256-CBC, sehingga tidak bisa dimanipulasi.

---

## Initialization

Session otomatis diinisialisasi saat memanggil class `Session`.
Driver dan lifetime mengikuti ENV:

```dotenv
SESSION_DRIVER=file        # file / redis / database
SESSION_LIFETIME=120       # dalam detik
APP_KEY=JFayvQsRn8xQVCcv0kjav3A23p3yzFgHzIl7+pTvuEw=
```

```php
use Caracal\Core\Session;

// Otomatis membaca ENV
$session = new Session();
```

> Tidak perlu `session_start()`, class Session akan menanganinya.

---

## Basic Usage

### Set & Get Data

```php
$session->set('user_id', 123);
$userId = $session->get('user_id'); // 123
```

Jika key tidak ada, bisa pakai default value:

```php
$role = $session->get('role', 'guest'); // 'guest' jika tidak ada
```

### Check Existence

```php
if ($session->has('user_id')) {
    echo "User is logged in";
}
```

### Remove Data

```php
$session->remove('user_id');
```

---

## Flash Data

Flash data adalah data **satu kali tampil**, biasanya untuk notifikasi atau pesan sukses/error.

```php
// Set flash
$session->flash('success', 'Data saved successfully');

// Get flash
$message = $session->flash('success'); // 'Data saved successfully'

// Flash otomatis hilang setelah dibaca
$session->flash('success'); // null
```

---

## Session ID & Regeneration

Regenerasi session ID penting untuk mencegah **session fixation attack**.

```php
// Ambil session ID saat ini
$id = $session->id();

// Regenerate session ID
$session->regenerate();
```

---

## Retrieve All Session Data

```php
$data = $session->all();
// Mengembalikan array semua session kecuali flash, semua sudah didekripsi
```

---

## Clear Session

Menghapus seluruh session, termasuk cookie dan Redis/DB jika digunakan:

```php
$session->clear();
```

> Setelah clear, session harus dibuat ulang jika ingin dipakai lagi.

---

## Security Features

1. **Encryption**

   * Menggunakan **AES-256-CBC**
   * Key diambil dari `APP_KEY` di `.env`
   * Semua session dienkripsi sehingga data aman di server

2. **HMAC Signing**

   * Memastikan data session **tidak dimanipulasi**
   * Jika integritas gagal, `get()` mengembalikan default

3. **Flash Data Aman**

   * Flash data juga terenkripsi dan HMAC-signed

---

## Supported Drivers

| Driver   | Description                                     | Config via ENV          |
| -------- | ----------------------------------------------- | ----------------------- |
| file     | Default, menggunakan `storage/sessions`         | SESSION_DRIVER=file     |
| redis    | Menggunakan Predis, tersambung ke Redis server  | SESSION_DRIVER=redis    |
| database | Tersimpan di tabel `sessions` (otomatis dibuat) | SESSION_DRIVER=database |

> Lifetime mengikuti `SESSION_LIFETIME` (detik)

---

## Example: Login & Flash Message

```php
$session = new \Caracal\Core\Session();

// Login
$session->set('user_id', $user->id);
$session->set('role', $user->role);

// Set flash message
$session->flash('success', 'Welcome back!');

// Redirect user
header('Location: /dashboard');
exit;

// In dashboard page
$flash = $session->flash('success');
if ($flash) {
    echo "<div class='alert alert-success'>$flash</div>";
}
```

---

## Notes

* Default **storage path**: `storage/sessions`
* Default **cookie settings**: `HttpOnly`, `SameSite=Lax`
* Lifetime default: 2 jam (`7200 detik`) jika ENV tidak diatur
* Flash data **otomatis hilang** setelah dibaca
* Driver **Redis** dan **Database** siap pakai
* Tidak ada dependency lain selain `APP_KEY` untuk enkripsi