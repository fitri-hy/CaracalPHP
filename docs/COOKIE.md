# CaracalPHP – Cookie Usage Documentation

Class:

```
Caracal\Core\Cookie
```

Cookie menyediakan utilitas untuk mengelola HTTP cookie dalam aplikasi Caracal.

Semua method bersifat **static**.

---

# Membuat Cookie

Gunakan method:

```
Cookie::set()
```

Signature:

```
set(
    string $name,
    mixed $value,
    int $minutes = 60,
    string $path = '/',
    string $domain = '',
    bool $secure = null,
    bool $httponly = true,
    string $samesite = 'Lax'
)
```

---

# Contoh Dasar

```
Cookie::set('username', 'john', 120);
```

Cookie berlaku selama **120 menit**.

---

# Menyimpan Array / Object

```
Cookie::set('user', [
    'id' => 1,
    'name' => 'John'
]);
```

Data akan otomatis disimpan sebagai **JSON**.

---

# Cookie Permanen

```
Cookie::forever('remember_token', 'abc123');
```

Cookie berlaku sekitar **5 tahun**.

---

# Mengambil Cookie

```
Cookie::get(string $name, mixed $default = null)
```

Contoh:

```
$username = Cookie::get('username');
```

Dengan default:

```
$username = Cookie::get('username', 'guest');
```

---

# Pull Cookie

Mengambil cookie lalu langsung menghapusnya.

```
$token = Cookie::pull('login_token');
```

---

# Mengecek Cookie

```
Cookie::has('username');
```

Return:

```
true / false
```

---

# Menghapus Cookie

```
Cookie::delete('username');
```

Cookie akan:

* expired
* dihapus dari `$_COOKIE`

---

# Menghapus Semua Cookie

```
Cookie::clearAll();
```

Semua cookie akan dihapus.

---

# Mengambil Semua Cookie

```
$cookies = Cookie::all();
```

---

# Default Security

Secara default cookie memiliki:

```
SameSite = Lax
HttpOnly = true
Secure   = auto jika HTTPS
```

Ini membantu melindungi dari:

* CSRF
* XSS
* cookie leakage

---

# Contoh Penggunaan di Controller

```
use Caracal\Core\Controller;
use Caracal\Core\Cookie;

class AuthController extends Controller
{
    public function login()
    {
        Cookie::set('user', 'john', 60);

        return $this->view('dashboard.view');
    }

    public function logout()
    {
        Cookie::delete('user');

        return $this->view('login.view');
    }
}
```

---

# Ringkasan Method

Method | Fungsi
set() | membuat cookie
get() | membaca cookie
pull() | membaca lalu menghapus cookie
forever() | cookie jangka sangat panjang
has() | mengecek cookie
delete() | menghapus cookie
clearAll() | menghapus semua cookie
all() | mengambil semua cookie