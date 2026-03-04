# 📘 CaracalPHP – Middleware Documentation

File ini berisi:

```php
Caracal\Core\MiddlewareInterface
Caracal\Core\Middleware
```

Middleware digunakan untuk:

* Memproses request sebelum controller
* Memodifikasi request/response
* Menghentikan eksekusi jika perlu
* Membuat pipeline eksekusi berantai

---

# 1️⃣ MiddlewareInterface

```php
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
```

Semua middleware harus mengimplementasikan method:

```php
handle(Request $request, callable $next): Response
```

---

## Parameter

| Parameter | Fungsi                             |
| --------- | ---------------------------------- |
| $request  | Object Request saat ini            |
| $next     | Closure untuk melanjutkan pipeline |

---

## Contoh Middleware

```php
use Caracal\Core\MiddlewareInterface;
use Caracal\Core\Request;
use Caracal\Core\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!isset($_SESSION['user'])) {
            return new Response('Unauthorized', 401);
        }

        return $next($request);
    }
}
```

---

# 2️⃣ Class Middleware (Pipeline Executor)

```php
class Middleware
```

Class ini bertugas:

* Menyimpan daftar middleware
* Menjalankan middleware secara berantai (pipeline pattern)

---

# 3️⃣ Method register()

```php
public function register(array $middlewares): void
```

Digunakan untuk mendaftarkan middleware.

Contoh:

```php
$middleware = new Middleware();

$middleware->register([
    AuthMiddleware::class,
    LogMiddleware::class,
]);
```

Array harus berisi nama class middleware.

---

# 4️⃣ Method run()

```php
public function run(Request $request, callable $final): Response
```

Method ini menjalankan seluruh middleware secara berantai.

---

## Cara Kerja Internal

```php
$stack = array_reduce(
    array_reverse($this->middlewares),
    fn($next, $middleware) =>
        fn($req) => (new $middleware())->handle($req, $next),
    $final
);
```

Penjelasan:

1. Middleware dibalik urutannya
2. Dibuat nested closure
3. Setiap middleware menerima:

   * Request
   * Next closure
4. Middleware terakhir akan memanggil `$final`

---

## Ilustrasi Pipeline

Jika middleware:

```php
[
    A::class,
    B::class,
    C::class
]
```

Maka eksekusi menjadi:

```
A → B → C → FINAL
```

---

# 5️⃣ Contoh Penggunaan Lengkap

```php
use Caracal\Core\Middleware;
use Caracal\Core\Request;
use Caracal\Core\Response;

$middleware = new Middleware();

$middleware->register([
    AuthMiddleware::class,
]);

$response = $middleware->run(
    Request::capture(),
    function ($request) {
        return new Response('Controller executed');
    }
);

$response->send();
```

---

# 📌 Pola Eksekusi

Setiap middleware harus:

* Memanggil `$next($request)` untuk lanjut
* Atau return `Response` untuk menghentikan pipeline

---

# 📌 Menghentikan Eksekusi

Jika middleware tidak memanggil `$next`, pipeline berhenti.

Contoh:

```php
return new Response('Forbidden', 403);
```

Controller tidak akan dijalankan.