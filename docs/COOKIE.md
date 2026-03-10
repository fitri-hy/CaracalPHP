# CaracalPHP – Cookie Documentation

Class

```php
Caracal\Core\Cookie
```

`Cookie` provides utilities for managing HTTP cookies within a Caracal application.

All methods are static.

---

## Creating a Cookie

Use the following method.

```
Cookie::set()
```

Signature

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

## Basic Example

```
Cookie::set('username', 'john', 120);
```

The cookie will be valid for **120 minutes**.

---

## Storing Arrays or Objects

```
Cookie::set('user', [
    'id' => 1,
    'name' => 'John'
]);
```

The data will automatically be stored as **JSON**.

---

## Permanent Cookie

```
Cookie::forever('remember_token', 'abc123');
```

This cookie will remain valid for approximately **five years**.

---

## Retrieving a Cookie

```
Cookie::get(string $name, mixed $default = null)
```

Example

```
$username = Cookie::get('username');
```

With a default value

```
$username = Cookie::get('username', 'guest');
```

---

## Pull Cookie

Retrieve a cookie and immediately delete it.

```
$token = Cookie::pull('login_token');
```

---

## Checking Cookie Existence

```
Cookie::has('username');
```

Return value

```
true / false
```

---

## Deleting a Cookie

```
Cookie::delete('username');
```

The cookie will be

Expired
Removed from `$_COOKIE`

---

## Deleting All Cookies

```
Cookie::clearAll();
```

All cookies will be removed.

---

## Retrieving All Cookies

```
$cookies = Cookie::all();
```

---

## Default Security Settings

By default, cookies use the following security configuration.

```
SameSite = Lax
HttpOnly = true
Secure   = automatic when using HTTPS
```

These settings help protect against

CSRF
XSS
Cookie leakage

---

## Example Usage in a Controller

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

## Method Summary

| Method     | Description                  |
| ---------- | ---------------------------- |
| set()      | Create a cookie              |
| get()      | Retrieve a cookie            |
| pull()     | Retrieve and delete a cookie |
| forever()  | Create a long-lived cookie   |
| has()      | Check if a cookie exists     |
| delete()   | Delete a cookie              |
| clearAll() | Delete all cookies           |
| all()      | Retrieve all cookies         |
