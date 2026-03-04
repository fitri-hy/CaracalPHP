# 📘 CaracalPHP – Cookie Usage Documentation

Class:

```php
Caracal\Core\Cookie
```

Class ini menyediakan static method untuk:

* Membuat cookie
* Membaca cookie
* Menghapus cookie
* Mengecek keberadaan cookie
* Menghapus semua cookie

Semua method bersifat **static**, sehingga tidak perlu membuat instance.

---

# 1️⃣ Membuat Cookie

Gunakan method:

```php
Cookie::set(
    string $name,
    mixed $value,
    int $minutes = 60,
    string $path = '/',
    string $domain = '',
    bool $secure = false,
    bool $httponly = true
): void
```

---

## Contoh Dasar

```php
use Caracal\Core\Cookie;

Cookie::set('username', 'john', 120);
```

Cookie akan aktif selama 120 menit.

---

## Menyimpan Array atau Object

Jika value berupa array atau object, otomatis akan di-serialize:

```php
Cookie::set('user_data', [
    'id' => 1,
    'name' => 'John'
], 60);
```

---

## Opsi Tambahan

```php
Cookie::set(
    'token',
    'abc123',
    30,
    '/',
    '',
    true,   // secure
    true    // httponly
);
```

Parameter:

| Parameter   | Fungsi                             |
| ----------- | ---------------------------------- |
| `$minutes`  | Durasi cookie dalam menit          |
| `$path`     | Path akses cookie                  |
| `$domain`   | Domain cookie (default: HTTP_HOST) |
| `$secure`   | Hanya dikirim via HTTPS            |
| `$httponly` | Tidak bisa diakses via JavaScript  |
| `samesite`  | Default: `Lax` (otomatis)          |

---

# 2️⃣ Mengambil Cookie

Gunakan:

```php
Cookie::get(string $name, mixed $default = null): mixed
```

---

## Contoh

```php
$username = Cookie::get('username');
```

Jika cookie tidak ada:

```php
$username = Cookie::get('username', 'guest');
```

---

## Otomatis Unserialize

Jika cookie sebelumnya disimpan sebagai array/object,
maka akan otomatis di-unserialize:

```php
$user = Cookie::get('user_data');

echo $user['name'];
```

---

# 3️⃣ Mengecek Apakah Cookie Ada

```php
Cookie::has('username');
```

Mengembalikan:

```php
true atau false
```

---

# 4️⃣ Menghapus Cookie

Gunakan:

```php
Cookie::delete(string $name, string $path = '/', string $domain = '')
```

Contoh:

```php
Cookie::delete('username');
```

Cookie akan dihapus dengan:

* Expired time di-set ke masa lalu
* Otomatis di-unset dari `$_COOKIE`

---

# 5️⃣ Menghapus Semua Cookie

```php
Cookie::clearAll();
```

Method ini akan:

* Loop seluruh `$_COOKIE`
* Menghapus satu per satu menggunakan `delete()`

---

# 6️⃣ Contoh Penggunaan di Controller

```php
use Caracal\Core\Controller;
use Caracal\Core\Cookie;

class AuthController extends Controller
{
    public function login()
    {
        Cookie::set('user', 'john', 60);

        return $this->render('dashboard.view');
    }

    public function logout()
    {
        Cookie::delete('user');

        return $this->render('login.view');
    }
}
```

---

# 7️⃣ Perilaku Internal Penting

### ✔ Array/Object otomatis di-serialize

### ✔ Saat `get()`, otomatis mencoba `unserialize()`

### ✔ `samesite` default adalah `'Lax'`

### ✔ Domain default menggunakan `$_SERVER['HTTP_HOST']`

### ✔ Cookie langsung tersedia di `$_COOKIE` setelah `set()`

---

# 📌 Ringkasan Method

| Method     | Fungsi                     |
| ---------- | -------------------------- |
| set()      | Membuat cookie             |
| get()      | Mengambil cookie           |
| delete()   | Menghapus cookie           |
| has()      | Mengecek keberadaan cookie |
| clearAll() | Menghapus semua cookie     |