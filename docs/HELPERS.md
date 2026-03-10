# CaracalPHP – Helpers Documentation

Class

```php
Caracal\Core\Helpers
```

This class contains static utility methods used for:

* Quick debugging (`dd`)
* Reading environment variables (`env`)
* Generating absolute URLs (`url`)

All methods are **static**, so you do not need to create an instance.

---

# `dd()` – Dump and Die

Method

```php
Helpers::dd(mixed $var): void
```

Purpose

* Displays the variable using `var_dump`
* Wraps the output in `<pre>` for readability
* Stops script execution using `exit`

---

## Example

```php
use Caracal\Core\Helpers;

Helpers::dd($user);
```

Output

* Full structure of the variable
* Script execution stops immediately

---

## When to Use

✔ Debugging during development
✔ Inspecting arrays or objects
✔ Quick investigation without a logger

⚠ Do not use in production.

---

# `env()` – Get Environment Variable

Method

```php
Helpers::env(string $key, mixed $default = null): mixed
```

Purpose

* Retrieves a value from `$_ENV`
* If not found, tries `getenv()`
* If still not found, returns the provided `$default`

---

## Example

```php
Helpers::env('APP_ENV');
```

With a default value

```php
Helpers::env('APP_DEBUG', false);
```

---

## Internal Behavior

```php
return $_ENV[$key] ?? getenv($key) ?? $default;
```

Priority order

1. `$_ENV`
2. `getenv()`
3. `$default`

---

# `url()` – Generate Absolute URL

Method

```php
Helpers::url(string $path = ''): string
```

Purpose

* Generates an absolute URL based on `APP_URL`
* Automatically adjusts for server port
* Appends the provided path

---

## Internal Process

1. Read `APP_URL` from environment
2. Detect scheme (`http` or `https`)
3. Determine host
4. Read `$_SERVER['SERVER_PORT']`
5. Append port if it is not default
6. Combine with the provided path

---

## Example

If `.env` contains

```env
APP_URL=http://localhost
```

And the server runs on port **8000**

```php
echo Helpers::url('login');
```

Result

```
http://localhost:8000/login
```

---

## HTTPS Example

If `.env` contains

```env
APP_URL=https://example.com
```

Then

```php
Helpers::url('dashboard');
```

Result

```
https://example.com/dashboard
```

---

# Important Behavior

✔ Default `APP_URL` is `http://localhost`
✔ Port is automatically appended if not **80** or **443**
✔ Double slashes in paths are automatically cleaned
✔ Does not parse subfolders from `APP_URL`
✔ Does not automatically handle query strings

---

# Method Summary

| Method | Purpose                          |
| ------ | -------------------------------- |
| dd()   | Dump variable and stop execution |
| env()  | Retrieve environment variables   |
| url()  | Generate absolute URLs           |
