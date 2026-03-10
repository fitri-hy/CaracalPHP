CaracalPHP – Session Documentation

## Overview

`Caracal\Core\Session` adalah **session engine aman dan fleksibel** untuk CaracalPHP.

Session ini dirancang untuk:

* Aman untuk production
* Mendukung **multi-driver storage**
* Menggunakan **enkripsi AES-256**
* Mendukung **flash message**
* Mendukung **lazy session start** (session hanya dibuat saat dipakai)

Semua data session disimpan dalam kondisi **terenkripsi dan HMAC-signed**, sehingga tidak dapat dimanipulasi.

---

# Features

| Feature              | Status |
| -------------------- | ------ |
| AES-256 Encryption   | ✅      |
| HMAC Data Integrity  | ✅      |
| Flash Message        | ✅      |
| Lazy Session Start   | ✅      |
| Redis Driver         | ✅      |
| Database Driver      | ✅      |
| Secure Cookie        | ✅      |
| Session Regeneration | ✅      |

---

# Supported Drivers

Session dapat menggunakan beberapa storage backend.

| Driver     | Description                             |
| ---------- | --------------------------------------- |
| `file`     | Default, disimpan di `storage/sessions` |
| `redis`    | Disimpan di Redis server                |
| `database` | Disimpan di tabel `sessions`            |

---

# Configuration (.env)

```dotenv
SESSION_DRIVER=file
SESSION_LIFETIME=7200
SESSION_COOKIE=caracal_session

APP_KEY=your-secret-key
```

Penjelasan:

| Variable         | Description                  |
| ---------------- | ---------------------------- |
| SESSION_DRIVER   | file / redis / database      |
| SESSION_LIFETIME | lifetime session dalam detik |
| SESSION_COOKIE   | nama cookie session          |
| APP_KEY          | key enkripsi session         |

---

# Initialization

Session otomatis diinisialisasi saat class dipanggil.

```php
use Caracal\Core\Session;

$session = new Session();
```

⚠ Tidak perlu memanggil `session_start()`.

Session menggunakan **lazy start**, artinya session baru dimulai ketika:

```
set()
get()
flash()
id()
```

dipanggil.

---

# Basic Usage

## Set Session Data

```php
$session->set('user_id', 123);
```

---

## Get Session Data

```php
$userId = $session->get('user_id');
```

Dengan default value:

```php
$role = $session->get('role', 'guest');
```

---

## Check Session Key

```php
if ($session->has('user_id')) {
    echo "User logged in";
}
```

---

## Remove Session Data

```php
$session->remove('user_id');
```

---

# Flash Message

Flash data adalah data yang hanya tersedia **untuk request berikutnya**.

Biasanya digunakan untuk:

* notifikasi sukses
* pesan error
* alert UI

---

## Set Flash

```php
$session->flash('success', 'Data saved successfully');
```

---

## Get Flash

```php
$message = $session->flash('success');
```

Setelah dibaca, flash akan otomatis dihapus.

Lifecycle flash:

```
Request 1 -> set flash
Request 2 -> flash tersedia
Request 3 -> flash hilang
```

---

# Session ID

Ambil session ID aktif:

```php
$id = $session->id();
```

---

# Regenerate Session ID

Digunakan untuk mencegah **session fixation attack**.

Contoh saat login:

```php
$session->regenerate();
```

---

# Retrieve All Session Data

```php
$data = $session->all();
```

Return:

```
array semua session (flash tidak disertakan)
```

Semua data otomatis **didekripsi**.

---

# Clear Session

Menghapus seluruh session:

```php
$session->clear();
```

Ini akan:

* menghapus data session
* menghancurkan session ID
* menghapus cookie session

---

# Encryption

Semua session data menggunakan:

```
AES-256-CBC encryption
HMAC-SHA256 signing
```

Struktur payload:

```
base64(
   IV
   HMAC
   CIPHERTEXT
)
```

Keuntungan:

* data tidak bisa dibaca langsung
* data tidak bisa dimodifikasi
* integritas payload terjamin

Jika payload rusak:

```
get() akan mengembalikan default value
```

---

# Redis Session Driver

Jika menggunakan Redis:

```dotenv
SESSION_DRIVER=redis
```

Session disimpan dengan format key:

```
caracal_session:{session_id}
```

Contoh:

```
caracal_session:2f1d8a73a9e5c1
```

⚠ Redis tidak menggunakan `flushdb()`, sehingga aman untuk multi aplikasi.

---

# Database Session Driver

Jika menggunakan database:

```dotenv
SESSION_DRIVER=database
```

Caracal akan otomatis membuat tabel:

```sql
sessions
```

Schema:

```sql
id VARCHAR PRIMARY KEY
payload TEXT
expires INT
```

Session akan otomatis dihapus oleh **garbage collector** saat expired.

---

# File Session Driver

Default driver.

Lokasi penyimpanan:

```
storage/sessions
```

Setiap session disimpan sebagai file.

Cocok untuk:

* development
* small applications
* single server deployment

---

# Security

Caracal Session memiliki beberapa fitur keamanan:

### Encryption

Semua data session terenkripsi menggunakan:

```
AES-256-CBC
```

---

### HMAC Verification

Payload ditandatangani menggunakan:

```
HMAC-SHA256
```

Jika payload dimodifikasi:

```
decrypt() akan gagal
```

---

### Secure Cookie

Session cookie menggunakan default:

```
HttpOnly
SameSite=Lax
Secure (HTTPS)
```

Sehingga:

* tidak bisa diakses JavaScript
* terlindung dari CSRF sebagian
* aman di HTTPS

---

# Example: Login Flow

```php
$session = new \Caracal\Core\Session();

// set user session
$session->set('user_id', $user->id);
$session->set('role', $user->role);

// regenerate ID
$session->regenerate();

// set flash message
$session->flash('success', 'Welcome back!');

header('Location: /dashboard');
exit;
```

Di halaman dashboard:

```php
$session = new Session();

if ($msg = $session->flash('success')) {
    echo "<div class='alert'>$msg</div>";
}
```

---

# Best Practice

Gunakan session untuk:

* authentication state
* flash message
* CSRF token
* user preference

Jangan gunakan session untuk:

* menyimpan file besar
* menyimpan data sensitif tanpa enkripsi tambahan
* menyimpan cache

---

# Summary

| Capability           | Supported |
| -------------------- | --------- |
| Multi Driver Session | ✅         |
| Encryption           | ✅         |
| Flash Message        | ✅         |
| Redis Support        | ✅         |
| Database Session     | ✅         |
| Lazy Start           | ✅         |
| Secure Cookies       | ✅         |