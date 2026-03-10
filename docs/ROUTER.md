# CaracalPHP – Router Documentation

## Route Location

```
app/Modules/*/Routes/*.php
```

---

# Basic Route

```php
return [
    ['GET', '/users', UserController::class.'@index']
];
```

---

# HTTP Helpers

Router sekarang menyediakan helper:

```php
$router->get('/users', UserController::class.'@index');
$router->post('/users', UserController::class.'@store');
$router->put('/users/{id}', UserController::class.'@update');
$router->delete('/users/{id}', UserController::class.'@destroy');
```

---

# Multiple Methods

```php
['GET|POST', '/login', AuthController::class.'@login']
```

---

# Route Parameters

```
/users/{id}
```

Controller:

```php
public function show(int $id)
```

---

# Route Group

```php
$router->group([
    'prefix' => '/api'
], function ($router) {

    $router->get('/users', UserController::class.'@index');

});
```

---

# Middleware

```php
[
 'GET',
 '/dashboard',
 DashboardController::class.'@index',
 ['middleware' => [AuthMiddleware::class]]
]
```

---

# Named Route

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

# Dependency Injection

Supported:

```
Request
Service Class
Route Parameter
```

Example:

```php
public function show(Request $request, int $id, UserService $service)
```

---

# Response Handling

Controller boleh return:

### String

```
return "OK";
```

### Array

```
return ['success'=>true];
```

Auto JSON.

### Response

```
return new Response('Done',200);
```

---

# Route Cache

Route otomatis di-cache.

Cache file:

```
storage/cache/routes.php
```

Untuk clear:

```php
Application::getInstance()->cache()->clearAll();
```

---

# Feature Summary

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