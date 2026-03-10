# CaracalPHP – Router Documentation

## Route Location

All module routes are located at:

```
app/Modules/*/Routes/*.php
```

---

Basic Route:

```php
return [
    ['GET', '/users', UserController::class.'@index']
];
```

---

HTTP Helpers:

The router provides convenient helpers for common HTTP methods:

```php
$router->get('/users', UserController::class.'@index');
$router->post('/users', UserController::class.'@store');
$router->put('/users/{id}', UserController::class.'@update');
$router->delete('/users/{id}', UserController::class.'@destroy');
```

---

Multiple Methods:

```php
['GET|POST', '/login', AuthController::class.'@login']
```

---

Route Parameters:

```
/users/{id}
```

Controller example:

```php
public function show(int $id)
```

---

Route Group:

```php
$router->group([
    'prefix' => '/api'
], function ($router) {

    $router->get('/users', UserController::class.'@index');

});
```

---

Middleware:

```php
[
    'GET',
    '/dashboard',
    DashboardController::class.'@index',
    ['middleware' => [AuthMiddleware::class]]
]
```

---

Named Route:

```php
[
    'GET',
    '/users/{id}',
    UserController::class.'@show',
    ['name' => 'users.show']
]
```

Generate URL:

```php
$router->route('users.show', ['id' => 5]);
```

Result:

```
/users/5
```

---

Dependency Injection:

The router supports automatic injection of:

* `Request` objects
* Service classes
* Route parameters

Example:

```php
public function show(Request $request, int $id, UserService $service)
```

---

Response Handling:

Controllers can return:

**String:**

```php
return "OK";
```

**Array:**

```php
return ['success' => true];
```

Automatically converted to JSON.

**Response Object:**

```php
return new Response('Done', 200);
```

---

Route Cache:

Routes are automatically cached.

Cache file location:

```
storage/cache/routes.php
```

Clear cache:

```php
Application::getInstance()->cache()->clearAll();
```

---

Feature Summary:

| Feature               | Supported |
| --------------------- | --------- |
| FastRoute Engine      | ✅         |
| Route Cache           | ✅         |
| Route Parameters      | ✅         |
| Named Route           | ✅         |
| Route Groups          | ✅         |
| Middleware            | ✅         |
| Dependency Injection  | ✅         |
| Multiple HTTP Methods | ✅         |
| JSON Auto Response    | ✅         |
| Base Path Support     | ✅         |