# CaracalPHP – CSRF Protection Documentation

Class:

```
Caracal\Core\CSRF
```

Class ini menyediakan proteksi terhadap **Cross-Site Request Forgery (CSRF)**.

Digunakan untuk:

* generate CSRF token
* menyisipkan token ke form
* memvalidasi token saat request
* proteksi POST / PUT / DELETE

---

# Cara Kerja CSRF di Caracal

1 Token dibuat menggunakan

```
random_bytes()
```

2 Token disimpan di **Session**

3 Token dikirim melalui:

```
hidden input form
atau header X-CSRF-TOKEN
```

4 Saat request masuk token divalidasi

5 Token hanya dapat digunakan **sekali**

---

# Generate Token

```php
$csrf = new CSRF();

$token = $csrf->generate();
```

Token otomatis disimpan di session.

---

# Menyisipkan Token ke Form

Cara paling mudah:

```php
<?= (new \Caracal\Core\CSRF())->inputField(); ?>
```

Output:

```html
<input type="hidden" name="_csrf" value="TOKEN">
```

---

# Contoh Form

```php
<form method="POST" action="/submit">

    <?= (new \Caracal\Core\CSRF())->inputField(); ?>

    <input type="text" name="name">

    <button type="submit">Submit</button>

</form>
```

---

# Validasi Token Manual

```php
$csrf = new CSRF();

$token = $_POST['_csrf'] ?? null;

if (!$csrf->validate($token)) {
    die('Invalid CSRF token');
}
```

---

# Validasi POST Otomatis

```php
$csrf->checkPost();
```

Contoh di controller:

```php
public function store()
{
    $csrf = new CSRF();

    if (!$csrf->checkPost()) {
        die('CSRF validation failed');
    }

    // lanjut proses
}
```

---

# Validasi AJAX / API

Gunakan header:

```
X-CSRF-TOKEN
```

Contoh JavaScript:

```javascript
fetch('/api/post', {
  method: 'POST',
  headers: {
    'X-CSRF-TOKEN': token
  }
});
```

Validasi di server:

```php
$csrf = new CSRF();

if (!$csrf->checkHeader()) {
    die('Invalid CSRF token');
}
```

---

# Mengambil Token Manual

Jika ingin inject ke JS:

```php
$token = (new CSRF())->token();
```

Contoh:

```php
<script>
const csrf = "<?= (new CSRF())->token() ?>";
</script>
```

---

# Menghapus Semua Token

```php
$csrf->clear();
```

Session CSRF akan dibersihkan.

---

# Perilaku Keamanan

Token memiliki fitur berikut:

* berbasis `random_bytes`
* disimpan di session
* **single-use**
* memiliki **TTL (30 menit)**
* mendukung **multi form token**

---

# Ringkasan Method

Method | Fungsi
generate() | membuat token
token() | alias generate
inputField() | membuat hidden input
validate() | memvalidasi token
checkPost() | validasi POST
checkHeader() | validasi header AJAX
clear() | menghapus semua token