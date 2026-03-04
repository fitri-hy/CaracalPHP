# 📘 CaracalPHP – Mailer Documentation

Class:

```php
Caracal\Core\Mailer
```

`Mailer` adalah wrapper sederhana untuk PHPMailer yang digunakan untuk mengirim email menggunakan SMTP berdasarkan konfigurasi aplikasi.

---

# 🎯 Tujuan Mailer

* Mengirim email via SMTP
* Menggunakan konfigurasi dari `Config`
* Mendukung HTML dan plain text
* Logging otomatis jika gagal

---

# 1️⃣ Sumber Konfigurasi

Semua konfigurasi diambil dari:

```php
Application::getInstance()->config()
```

Key config yang digunakan:

| Key Config        | Fungsi         |
| ----------------- | -------------- |
| mail.host         | SMTP host      |
| mail.user         | SMTP username  |
| mail.pass         | SMTP password  |
| mail.encryption   | tls / ssl      |
| mail.port         | SMTP port      |
| mail.from_address | Email pengirim |
| mail.from_name    | Nama pengirim  |

---

# 2️⃣ Constructor

```php
public function __construct()
```

Saat Mailer dibuat:

1. Mengambil instance Config
2. Mengaktifkan mode SMTP
3. Mengatur kredensial SMTP
4. Mengatur port dan encryption
5. Mengatur default sender
6. Set charset UTF-8

Konfigurasi internal:

```php
$this->mailer->isSMTP();
$this->mailer->SMTPAuth = true;
$this->mailer->CharSet = 'UTF-8';
```

---

# 3️⃣ Method send()

```php
public function send(string $to, string $subject, string $body, bool $isHtml = true): bool
```

### Parameter

| Parameter | Fungsi                 |
| --------- | ---------------------- |
| $to       | Email tujuan           |
| $subject  | Subject email          |
| $body     | Isi email              |
| $isHtml   | Format HTML atau tidak |

Return:

* `true` → jika berhasil
* `false` → jika gagal

---

## Cara Kerja Internal

Sebelum mengirim:

```php
$this->mailer->clearAllRecipients();
$this->mailer->addAddress($to);
$this->mailer->isHTML($isHtml);
```

Kemudian:

```php
$this->mailer->send();
```

---

# 4️⃣ Logging Jika Gagal

Jika terjadi exception:

```php
(new Logger('mailer'))->error(...)
```

Log akan disimpan ke:

```text
/storage/logs/mailer.log
```

Context yang disimpan:

* to
* subject

---

# 📌 Contoh Penggunaan

```php
use Caracal\Core\Mailer;

$mailer = new Mailer();

$success = $mailer->send(
    'user@example.com',
    'Welcome to CaracalPHP',
    '<h1>Hello User</h1><p>Your account is ready.</p>'
);

if (!$success) {
    echo "Email gagal dikirim.";
}
```

---

# 📌 Mengirim Plain Text

```php
$mailer->send(
    'user@example.com',
    'Plain Text Mail',
    'Ini adalah email text biasa.',
    false
);
```