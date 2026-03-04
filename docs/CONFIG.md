# 📘 CaracalPHP – Config Usage Documentation

Class konfigurasi:

```php
Caracal\Core\Config
```

Config bertugas untuk:

* Memuat file `.env` (sekali saja)
* Memuat `config/config.php`
* Menyediakan akses konfigurasi dengan dot notation
* Menyediakan override sederhana saat runtime

Config biasanya diakses melalui:

```php
Application::getInstance()->config();
```

---

# 1️⃣ Mengambil Nilai Konfigurasi

Gunakan method:

```php
get(string $key, mixed $default = null)
```

Config mendukung **dot notation** untuk membaca nested array.

---

## Contoh Struktur `config/config.php`

```php
return [
    'app' => [
        'name' => 'CaracalPHP',
        'env'  => 'local',
    ],
    'database' => [
        'host' => '127.0.0.1'
    ]
];
```

---

## Cara Mengambil Nilai

```php
use Caracal\Core\Application;

$config = Application::getInstance()->config();

$appName = $config->get('app.name');
$appEnv  = $config->get('app.env');
$dbHost  = $config->get('database.host');
```

---

## Menggunakan Default Value

Jika key tidak ditemukan:

```php
$timezone = $config->get('app.timezone', 'UTC');
```

Jika `app.timezone` tidak ada, maka `"UTC"` akan dikembalikan.

---

# 2️⃣ Cara Kerja Dot Notation

Method `get()` akan:

1. Memecah key berdasarkan `.`
2. Menelusuri array bertingkat
3. Mengembalikan default jika salah satu segment tidak ditemukan

Contoh:

```
app.name
```

akan diakses sebagai:

```php
$config['app']['name']
```

---

# 3️⃣ Penggunaan di Controller

```php
use Caracal\Core\Application;

class HomeController
{
    public function index()
    {
        $config = Application::getInstance()->config();

        $appName = $config->get('app.name');

        return $this->view('home.view', [
            'appName' => $appName
        ]);
    }
}
```

---

# 4️⃣ Penggunaan di Service

```php
use Caracal\Core\Application;

class HomeService
{
    public function isLocal(): bool
    {
        $config = Application::getInstance()->config();

        return $config->get('app.env') === 'local';
    }
}
```

---

# 5️⃣ Penggunaan di Middleware

```php
use Caracal\Core\Application;

public function handle($request, $next)
{
    $config = Application::getInstance()->config();

    if ($config->get('app.env') === 'maintenance') {
        echo "Application under maintenance.";
        return;
    }

    return $next($request);
}
```

---

# 6️⃣ Override Konfigurasi Saat Runtime

Tersedia method:

```php
set(string $key, mixed $value)
```

Contoh:

```php
$config = Application::getInstance()->config();

$config->set('app.debug', true);
```

⚠ **Penting (Sesuai Implementasi Asli)**

Method `set()` **tidak mendukung nested update menggunakan dot notation**.

Artinya:

```php
$config->set('app.name', 'NewName');
```

Tidak akan mengubah:

```php
['app' => ['name' => '...']]
```

Tetapi akan menghasilkan key baru:

```php
[
    'app' => [...],
    'app.name' => 'NewName'
]
```

Jadi `set()` hanya menyimpan key secara literal.

Gunakan hanya untuk override sederhana.

---

# 7️⃣ Mengambil Semua Konfigurasi

```php
$config = Application::getInstance()->config();

$all = $config->all();
```

Method `all()` mengembalikan seluruh array konfigurasi yang sudah dimuat.

---

# 📌 Ringkasan Method

| Method | Fungsi                                     |
| ------ | ------------------------------------------ |
| get()  | Mengambil nilai config dengan dot notation |
| set()  | Menambahkan / override key literal         |
| all()  | Mengambil seluruh konfigurasi              |