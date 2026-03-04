# 📘 CaracalPHP – Response Documentation

Class:

```php id="k3x8fp"
Caracal\Core\Response
```

`Response` adalah representasi HTTP response dalam CaracalPHP.

Class ini bertanggung jawab untuk:

* Menyimpan konten response
* Menentukan HTTP status code
* Mengatur header
* Mengirim output ke browser

Digunakan oleh:

* Controller
* Kernel
* ErrorHandler
* Middleware

---

# 🎯 Tujuan Response

* Standarisasi output HTTP
* Mendukung response HTML
* Mendukung JSON response
* Mendukung redirect

---

# 1️⃣ Properti Internal

```php id="s4hl8c"
protected string $content;
protected int $status;
protected array $headers;
```

Penjelasan:

| Property | Fungsi            |
| -------- | ----------------- |
| $content | Isi body response |
| $status  | HTTP status code  |
| $headers | Header tambahan   |

---

# 2️⃣ Constructor

```php id="y8j2nb"
public function __construct(
    string $content = '',
    int $status = 200,
    array $headers = []
)
```

Contoh penggunaan:

```php id="q7m1sv"
return new Response('<h1>Hello</h1>', 200);
```

Dengan header custom:

```php id="cmg4vr"
return new Response(
    'Created',
    201,
    ['X-Custom-Header' => 'Value']
);
```

---

# 3️⃣ Method send()

```php id="5ztg9a"
public function send(): void
```

Method ini:

1. Mengatur HTTP status
2. Mengirim semua header
3. Menampilkan content

Implementasi:

```php id="g2sd7k"
http_response_code($this->status);

foreach ($this->headers as $key => $value) {
    header("$key: $value");
}

echo $this->content;
```

Biasanya dipanggil oleh Kernel:

```php id="o6j3mw"
$response->send();
```

---

# 4️⃣ Static Method json()

```php id="3f8lqk"
public static function json(
    mixed $data,
    int $status = 200,
    array $headers = []
): self
```

Digunakan untuk membuat JSON response dengan mudah.

---

## Fitur

* Auto set `Content-Type: application/json`
* Pretty print
* Unicode tidak di-escape

---

## Contoh

```php id="r3x6vz"
return Response::json([
    'success' => true,
    'message' => 'Data saved'
]);
```

Output:

```json id="v4bnr0"
{
    "success": true,
    "message": "Data saved"
}
```

---

## Dengan Status Custom

```php id="mt2q8l"
return Response::json(
    ['error' => 'Unauthorized'],
    401
);
```

---

## Dengan Header Tambahan

```php id="w1k8sr"
return Response::json(
    ['ok' => true],
    200,
    ['X-API-Version' => '1.0']
);
```

---

# 5️⃣ Static Method redirect()

```php id="p6y4nd"
public static function redirect(string $url, int $status = 302): self
```

Digunakan untuk redirect ke URL lain.

---

## Contoh

```php id="j9m2tr"
return Response::redirect('/login');
```

Setara dengan:

```http id="u5b8ye"
HTTP/1.1 302 Found
Location: /login
```

---

## Dengan Status 301

```php id="l8x2sv"
return Response::redirect('/new-url', 301);
```

---

# 📌 Contoh Penggunaan di Controller

### HTML Response

```php id="c2k9mv"
return new Response('<h1>Dashboard</h1>');
```

---

### JSON API

```php id="n4q7zx"
return Response::json([
    'user' => $user
]);
```

---

### Redirect Setelah Submit

```php id="e7d2lk"
return Response::redirect('/dashboard');
```