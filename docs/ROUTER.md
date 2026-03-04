# 📘 CaracalPHP – Route Usage Documentation

Routing adalah mekanisme untuk memetakan HTTP request ke controller yang sesuai.
CaracalPHP menggunakan pendekatan **array-based routing** yang eksplisit, modular, dan production-ready.

---

# 1. Lokasi Route

Semua route didefinisikan di dalam module:

```
app/Modules/*/Routes/*.php
```

Contoh struktur:

```
app/
 └── Modules/
      └── Test/
           ├── Controllers/
           └── Routes/
                └── web.php
```

Setiap file route **harus mengembalikan array**.

---

# 2. Format Dasar Route

```php
<?php
use App\Modules\Test\Controllers\TestController;

return [
    ['GET', '/test', TestController::class . '@index'],
];
```

Struktur array:

```
[
    HTTP_METHOD,
    URI_PATH,
    CONTROLLER@METHOD,
    [optional options]
]
```

---

# 3. HTTP Methods

Caracal mendukung method HTTP standar:

```php
return [

    ['GET', '/users', UserController::class . '@index'],

    ['POST', '/users', UserController::class . '@store'],

    ['PUT', '/users/{id}', UserController::class . '@update'],

    ['DELETE', '/users/{id}', UserController::class . '@destroy'],

];
```

---

# 4. Route Parameters

Gunakan `{parameter}` pada URI.

```php
['GET', '/users/{id}', UserController::class . '@show'],
```

Controller:

```php
public function show(int $id)
{
    return "User ID: {$id}";
}
```

Parameter akan di-inject otomatis berdasarkan nama.

---

# 5. Multiple Parameters

```php
['GET', '/posts/{post}/comments/{comment}', CommentController::class . '@show'],
```

Controller:

```php
public function show(int $post, int $comment)
```

---

# 6. Named Route

Named route digunakan untuk menghasilkan URL secara programatik.

```php
return [
    [
        'GET',
        '/users/{id}',
        UserController::class . '@show',
        ['name' => 'users.show']
    ],
];
```

Generate URL:

```php
$router->route('users.show', ['id' => 10]);
```

Hasil:

```
/users/10
```

---

# 7. Route Middleware

Middleware dapat ditambahkan pada route tertentu.

```php
return [
    [
        'GET',
        '/dashboard',
        DashboardController::class . '@index',
        ['middleware' => [AuthMiddleware::class]]
    ],
];
```

Multiple middleware:

```php
['middleware' => [
    AuthMiddleware::class,
    LogMiddleware::class
]]
```

Middleware akan dieksekusi sebelum controller dipanggil.

---

# 8. Dependency Injection

Router mendukung automatic dependency resolution.

## Constructor Injection

```php
class UserController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
}
```

## Method Injection

```php
public function show(Request $request, int $id, UserService $service)
```

Yang dapat di-inject otomatis:

* Route parameter
* Request instance
* Class dependency

---

# 9. Response Handling

Controller dapat mengembalikan:

### String

```php
return "OK";
```

### Array / Object

```php
return ['success' => true];
```

→ otomatis menjadi JSON response.

### Response Instance

```php
return new Response('Done', 200);
```

---

# 10. Base Path Support

Jika aplikasi dijalankan dalam subfolder:

```
http://localhost/project-name/test
```

Route tetap ditulis sebagai:

```php
['GET', '/test', TestController::class . '@index'],
```

Router akan otomatis menyesuaikan base path tanpa konfigurasi tambahan.

---

# 11. Route Caching

Route secara otomatis disimpan menggunakan `core/Cache`.

* Key cache: `routes`
* TTL mengikuti konfigurasi cache
* Mendukung file dan Redis driver

Untuk menghapus cache:

```php
Application::getInstance()->cache()->clearAll();
```

---

# 12. Contoh Lengkap

```php
<?php
use App\Modules\User\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;

return [

    ['GET', '/', UserController::class . '@home'],

    ['GET', '/users', UserController::class . '@index'],

    [
        'GET',
        '/users/{id}',
        UserController::class . '@show',
        ['name' => 'users.show']
    ],

    [
        'POST',
        '/users',
        UserController::class . '@store',
        ['middleware' => [AuthMiddleware::class]]
    ],
];
```

---

# Feature Summary

| Feature                 | Supported |
| ----------------------- | --------- |
| Simple Array Style      | ✅         |
| Route Parameters        | ✅         |
| Named Route             | ✅         |
| Middleware per Route    | ✅         |
| Constructor Injection   | ✅         |
| Method Injection        | ✅         |
| Automatic JSON Response | ✅         |
| Base Path Handling      | ✅         |
| Route Caching           | ✅         |