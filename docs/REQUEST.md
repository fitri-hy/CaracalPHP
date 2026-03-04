# 📘 CaracalPHP – Request Documentation

Class:

```php id="8yx2dn"
Caracal\Core\Request
```

`Request` adalah representasi HTTP request yang membungkus superglobal PHP:

* `$_GET`
* `$_POST`
* `$_SERVER`
* `$_COOKIE`
* `$_FILES`

Class ini menyediakan akses terstruktur dan lebih bersih terhadap data request.

---

# 🎯 Tujuan Request

* Membungkus superglobal
* Menyediakan akses method & URI
* Menyediakan helper input
* Digunakan oleh Router & Middleware

---

# 1️⃣ Properti Public

```php id="hztq9g"
public array $get, $post, $server, $cookies, $files;
```

Semua data disalin saat object dibuat.

Artinya:

* Snapshot request saat capture
* Tidak membaca superglobal ulang

---

# 2️⃣ Constructor (Private)

```php id="a3nvtk"
private function __construct()
```

Mengisi properti dari superglobal:

```php id="k5i1pn"
$this->get     = $_GET;
$this->post    = $_POST;
$this->server  = $_SERVER;
$this->cookies = $_COOKIE;
$this->files   = $_FILES;
```

Karena private → tidak bisa di-instantiate langsung.

---

# 3️⃣ Method capture()

```php id="p3sz6t"
public static function capture(): self
```

Digunakan untuk membuat instance Request.

Contoh:

```php id="74jix3"
$request = Request::capture();
```

Biasanya dipanggil oleh Kernel:

```php id="d7y2m0"
$request = Request::capture();
```

---

# 4️⃣ Method method()

```php id="6k5a1v"
public function method(): string
```

Mengembalikan HTTP method dalam huruf besar.

Contoh:

```php id="o7m8tr"
$request->method();
```

Hasil:

```text id="38xfwq"
GET
POST
PUT
DELETE
```

Jika tidak ada → default `GET`.

---

# 5️⃣ Method uri()

```php id="d2s9kv"
public function uri(): string
```

Mengambil path URI tanpa query string.

Contoh:

Jika URL:

```id="6d8vln"
/users?id=5
```

Maka:

```php id="q6o8dp"
$request->uri();
```

Hasil:

```id="x1h8rp"
/users
```

Menggunakan:

```php id="vm4xk2"
parse_url($uri, PHP_URL_PATH);
```

---

# 6️⃣ Method input()

```php id="3d2flh"
public function input(string $key, mixed $default = null): mixed
```

Mengambil nilai dari:

1. POST
2. GET
3. Default value

---

## Contoh

```php id="3jzshg"
$name = $request->input('name');
```

Urutan prioritas:

```php id="smjns0"
$_POST['name'] ?? $_GET['name'] ?? $default
```

---

## Dengan Default

```php id="0rthst"
$page = $request->input('page', 1);
```

---

# 7️⃣ Method all()

```php id="74l2jv"
public function all(): array
```

Menggabungkan GET dan POST:

```php id="d3hxsd"
array_merge($this->get, $this->post);
```

Catatan:

* Jika key sama → POST akan override GET

---

# 📌 Contoh Penggunaan di Controller

```php id="9k3lzv"
public function store(Request $request)
{
    $data = $request->all();

    $name = $request->input('name');
    $email = $request->input('email');

    return new Response("Saved {$name}");
}
```

---

# 📌 Akses File Upload

Karena `$files` public:

```php id="72a0lu"
$request->files['avatar'];
```

---

# 📌 Akses Cookie

```php id="o8j4mq"
$request->cookies['session'];
```

---

# ⚠ Perilaku Penting Sesuai Implementasi

✔ Tidak ada sanitasi otomatis
✔ Tidak ada validation
✔ Tidak ada JSON body parsing
✔ Tidak ada PUT/PATCH parsing
✔ Tidak immutable
✔ Snapshot sekali saat capture

---

# ⚠ Tidak Mendukung

* Raw JSON body (`php://input`)
* Method spoofing
* Header access helper
* IP helper
* AJAX detection