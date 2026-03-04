# 📘 CaracalPHP – Logger Documentation

Class:

```php
Caracal\Core\Logger
```

`Logger` adalah wrapper sederhana untuk Monolog yang digunakan untuk mencatat:

* Informasi (info)
* Error
* Warning

Log disimpan ke file di folder:

```
/storage/logs/
```

---

# 🎯 Tujuan Logger

* Menyediakan logging terstruktur
* Menyimpan log ke file
* Mendukung context array
* Menggunakan Monolog sebagai engine

---

# 1️⃣ Constructor

```php
public function __construct(string $name = 'app', string $file = 'app.log')
```

### Parameter

| Parameter | Default   | Fungsi              |
| --------- | --------- | ------------------- |
| $name     | 'app'     | Nama channel logger |
| $file     | 'app.log' | Nama file log       |

---

## Cara Kerja Internal

```php
$this->logger = new MonoLogger($name);
$path = __DIR__ . '/../storage/logs/' . $file;
$this->logger->pushHandler(new StreamHandler($path, MonoLogger::DEBUG));
```

Artinya:

* Semua level log disimpan (DEBUG ke atas)
* File otomatis berada di `/storage/logs/`
* Menggunakan StreamHandler

---

# 2️⃣ Method info()

```php
public function info(string $msg, array $context = []): void
```

Digunakan untuk mencatat informasi umum.

### Contoh:

```php
use Caracal\Core\Logger;

$logger = new Logger();
$logger->info('User login success', [
    'user_id' => 10
]);
```

---

# 3️⃣ Method error()

```php
public function error(string $msg, array $context = []): void
```

Digunakan untuk mencatat error.

### Contoh:

```php
$logger->error('Database connection failed', [
    'host' => 'localhost'
]);
```

---

# 4️⃣ Method warning()

```php
public function warning(string $msg, array $context = []): void
```

Digunakan untuk mencatat peringatan.

### Contoh:

```php
$logger->warning('API response slow', [
    'duration' => 3.5
]);
```

---

# 📌 Lokasi File Log

Secara default:

```
/core/../storage/logs/app.log
```

Artinya:

```
project-root/storage/logs/app.log
```

---

# 📌 Format Log (Default Monolog)

Contoh output di file:

```
[2026-03-04T10:15:32.000000+00:00] app.INFO: User login success {"user_id":10} []
```