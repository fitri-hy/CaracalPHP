# CaracalPHP Config Usage Documentation

Class konfigurasi:

```
Caracal\Core\Config
```

Config bertanggung jawab untuk:

* memuat `.env`
* memuat file konfigurasi dari folder `config/`
* menyediakan akses konfigurasi menggunakan **dot notation**
* mendukung override runtime
* menyediakan **config caching**

Config diakses melalui:

```
Application::getInstance()->config();
```

---

# Mengambil Konfigurasi

Gunakan:

```
get(string $key, mixed $default = null)
```

Contoh:

```
$config->get('app.name');
$config->get('db.host');
$config->get('mail.host');
```

---

# Dot Notation

Key:

```
app.name
```

akan mengakses:

```
$config['app']['name']
```

---

# Mengecek Konfigurasi

```
$config->has('app.name');
```

---

# Override Runtime

Sekarang `set()` mendukung dot notation.

```
$config->set('app.debug', true);
```

---

# Mengambil Environment Variable

```
$config->env('APP_ENV');
```

---

# Multiple Config Files

Folder:

```
config/
```

Contoh:

```
config/
   app.php
   database.php
   cache.php
```

Isi file:

```
return [
    'debug' => true
];
```

Akan tersedia sebagai:

```
$config->get('app.debug');
```

---

# Config Cache

Untuk production, konfigurasi bisa dikompilasi:

```
$config->cache();
```

File cache:

```
storage/cache/config.php
```

Membersihkan cache:

```
$config->clearCache();
```

---

# Mengambil Semua Config

```
$config->all();
```

---

# Ringkasan Method

Method | Fungsi
get() | mengambil config
set() | override config
has() | cek key
env() | ambil environment variable
cache() | compile config
clearCache() | hapus cache config
all() | semua konfigurasi