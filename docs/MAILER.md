# 📘 CaracalPHP – Mailer: Cara Pakai

Class:

```php
Caracal\Core\Mailer
```

Digunakan untuk mengirim email via SMTP berdasarkan konfigurasi di `.env` / Config.

---

## 1️⃣ Membuat Instance

```php
use Caracal\Core\Mailer;

$mailer = new Mailer();
```

Ini otomatis mengambil konfigurasi dari aplikasi (`mail.host`, `mail.user`, `mail.pass`, dll).

---

## 2️⃣ Mengirim Email HTML

```php
$success = $mailer->send(
    'user@example.com',          // Email tujuan
    'Welcome to CaracalPHP',     // Subject
    '<h1>Hello User</h1><p>Your account is ready.</p>' // Isi email (HTML)
);

if ($success) {
    echo "Email berhasil dikirim.";
} else {
    echo "Email gagal dikirim.";
}
```

---

## 3️⃣ Mengirim Email Plain Text

```php
$success = $mailer->send(
    'user@example.com',
    'Plain Text Mail',
    'Ini adalah email text biasa.',
    false  // false = plain text
);
```

---

## 4️⃣ Contoh di Controller

Misal di controller:

```php
namespace App\Modules\Welcome\Controllers;

use Caracal\Core\Controller;
use Caracal\Core\Mailer;

class WelcomeController extends Controller
{
    public function sendMail(): string
    {
        $mailer = new Mailer();

        $success = $mailer->send(
            'user@example.com',
            'Welcome!',
            '<h1>Selamat datang!</h1><p>Email ini dikirim dari CaracalPHP.</p>'
        );

        return $success ? 'Email berhasil dikirim' : 'Email gagal dikirim';
    }
}
```