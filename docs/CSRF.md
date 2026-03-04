# 📘 CaracalPHP – CSRF Protection Documentation

Class:

```php
Caracal\Core\CSRF
```

Class ini digunakan untuk:

* Generate CSRF token
* Menyisipkan token ke dalam form
* Memvalidasi token saat form dikirim
* Proteksi request POST dari CSRF attack

---

# 🎯 Cara Kerja CSRF di Caracal

1. Token dibuat menggunakan:

   * `random_bytes()`
   * `hash_hmac('sha256', ..., APP_KEY)`
2. Token disimpan di Session
3. Token dikirim melalui hidden input form
4. Saat POST, token divalidasi
5. Token langsung dihapus setelah validasi (single-use)

---

# 1️⃣ Membuat Instance

```php
use Caracal\Core\CSRF;

$csrf = new CSRF();
```

Class ini otomatis membuat instance `Session` di constructor:

```php
$this->session = new Session();
```

---

# 2️⃣ Generate Token Manual

```php
$token = $csrf->generate();
```

Yang terjadi:

* Token dibuat
* Disimpan ke session dengan key `_csrf_token`
* Token dikembalikan sebagai string

---

# 3️⃣ Menyisipkan Token ke Form (Cara Direkomendasikan)

Gunakan:

```php
<?= (new \Caracal\Core\CSRF())->inputField(); ?>
```

Method:

```php
public function inputField(): string
```

Akan menghasilkan:

```html
<input type="hidden" name="_csrf" value="TOKEN">
```

Token otomatis di-generate dan disimpan ke session.

---

# 4️⃣ Contoh Penggunaan di View

```php
<form method="POST" action="/submit">
    <?= (new \Caracal\Core\CSRF())->inputField(); ?>

    <input type="text" name="name">
    <button type="submit">Submit</button>
</form>
```

---

# 5️⃣ Validasi Token Manual

Gunakan:

```php
$isValid = $csrf->validate($token);
```

Contoh:

```php
$token = $_POST['_csrf'] ?? null;

if (!$csrf->validate($token)) {
    die('Invalid CSRF token');
}
```

---

# 6️⃣ Validasi Otomatis untuk POST

Gunakan:

```php
$csrf->checkPost();
```

Method ini:

```php
$token = $_POST['_csrf'] ?? null;
return $this->validate($token);
```

Contoh di Controller:

```php
public function store()
{
    $csrf = new \Caracal\Core\CSRF();

    if (!$csrf->checkPost()) {
        die('CSRF validation failed');
    }

    // lanjut proses
}
```

---

# 🔐 Perilaku Penting (Sesuai Implementasi)

### ✔ Token disimpan di Session dengan key:

```php
_csrf_token
```

### ✔ Token menggunakan:

```php
hash_hmac('sha256', random_bytes, APP_KEY)
```

### ✔ APP_KEY diambil dari:

```php
Helpers::env('APP_KEY', 'default_app_key')
```

### ✔ Token hanya berlaku sekali (single-use)

Karena pada validate():

```php
$this->session->delete($this->key);
```

Token langsung dihapus setelah dicek.