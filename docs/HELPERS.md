# 📘 CaracalPHP – Helpers Documentation

Class:

```php
Caracal\Core\Helpers
```

Class ini berisi utility static method untuk:

* Debug cepat (`dd`)
* Mengambil environment variable (`env`)
* Membuat URL absolut (`url`)

Semua method bersifat **static**, sehingga tidak perlu membuat instance.

---

# 1️⃣ `dd()` – Dump and Die

Method:

```php
Helpers::dd(mixed $var): void
```

Fungsi:

* Menampilkan isi variabel menggunakan `var_dump`
* Dibungkus dengan `<pre>`
* Menghentikan eksekusi (`exit`)

---

## Contoh

```php
use Caracal\Core\Helpers;

Helpers::dd($user);
```

Output:

* Struktur lengkap variabel
* Script langsung berhenti

---

## Kapan Digunakan?

✔ Debugging saat development
✔ Mengecek isi array / object
✔ Investigasi cepat tanpa logger

⚠ Jangan gunakan di production.

---

# 2️⃣ `env()` – Ambil Environment Variable

Method:

```php
Helpers::env(string $key, mixed $default = null): mixed
```

Fungsi:

* Mengambil value dari `$_ENV`
* Jika tidak ada → coba `getenv()`
* Jika tetap tidak ada → kembalikan `$default`

---

## Contoh

```php
Helpers::env('APP_ENV');
```

Dengan default:

```php
Helpers::env('APP_DEBUG', false);
```

---

## Cara Kerja Internal

```php
return $_ENV[$key] ?? getenv($key) ?? $default;
```

Artinya:

1. Prioritas `$_ENV`
2. Fallback ke `getenv()`
3. Terakhir gunakan default

---

# 3️⃣ `url()` – Generate Absolute URL

Method:

```php
Helpers::url(string $path = ''): string
```

Fungsi:

* Membuat URL absolut berdasarkan `APP_URL`
* Menyesuaikan port server
* Menggabungkan dengan path

---

## Cara Kerja Internal

1. Ambil `APP_URL` dari env
2. Ambil scheme (http/https)
3. Ambil host
4. Cek `$_SERVER['SERVER_PORT']`
5. Tambahkan port jika bukan default
6. Gabungkan dengan path

---

## Contoh

Jika `.env`:

```env
APP_URL=http://localhost
```

Dan server berjalan di port 8000:

```php
echo Helpers::url('login');
```

Hasil:

```text
http://localhost:8000/login
```

---

## Contoh HTTPS

Jika:

```env
APP_URL=https://example.com
```

Maka:

```php
Helpers::url('dashboard');
```

Hasil:

```text
https://example.com/dashboard
```

---

# 📌 Perilaku Penting Sesuai Implementasi

✔ Default `APP_URL` adalah `http://localhost`
✔ Port otomatis ditambahkan jika bukan 80/443
✔ Path otomatis dibersihkan dari double slash
✔ Tidak membaca subfolder dari APP_URL
✔ Tidak menangani query string otomatis

---

# 📌 Ringkasan Method

| Method | Fungsi                     |
| ------ | -------------------------- |
| dd()   | Dump & hentikan eksekusi   |
| env()  | Ambil environment variable |
| url()  | Generate absolute URL      |