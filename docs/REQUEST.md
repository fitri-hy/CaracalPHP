# CaracalPHP – Request Documentation

Class:

```php
Caracal\Core\Request
```

`Request` represents an HTTP request and wraps the PHP superglobals:

* `$_GET`
* `$_POST`
* `$_SERVER`
* `$_COOKIE`
* `$_FILES`

This class provides structured, clean access to request data.

---

Purpose of the Request class:

* Wrap superglobals
* Provide access to HTTP method and URI
* Offer input helper methods
* Used by Router and Middleware

---

Public Properties:

```php
public array $get, $post, $server, $cookies, $files;
```

All data is copied when the object is created:

* Captures a snapshot of the request at the time of capture
* Does not read superglobals again

---

Constructor (private):

```php
private function __construct()
```

Initializes properties from superglobals:

```php
$this->get     = $_GET;
$this->post    = $_POST;
$this->server  = $_SERVER;
$this->cookies = $_COOKIE;
$this->files   = $_FILES;
```

Being private, it cannot be instantiated directly.

---

Method `capture`:

```php
public static function capture(): self
```

Creates a new `Request` instance.

Example:

```php
$request = Request::capture();
```

Typically called by the Kernel:

```php
$request = Request::capture();
```

---

Method `method`:

```php
public function method(): string
```

Returns the HTTP method in uppercase.

Example:

```php
$request->method();
```

Output:

```text
GET
POST
PUT
DELETE
```

Default is `GET` if not set.

---

Method `uri`:

```php
public function uri(): string
```

Returns the path URI without the query string.

Example:

If the URL is:

```bash
/users?id=5
```

Then:

```php
$request->uri();
```

Output:

```bash
/users
```

Implementation uses:

```php
parse_url($uri, PHP_URL_PATH);
```

---

Method `input`:

```php
public function input(string $key, mixed $default = null): mixed
```

Fetches a value in the following order:

1. POST
2. GET
3. Default value

Example:

```php
$name = $request->input('name');
```

Priority order:

```php
$_POST['name'] ?? $_GET['name'] ?? $default
```

With default:

```php
$page = $request->input('page', 1);
```

---

Method `all`:

```php
public function all(): array
```

Merges GET and POST:

```php
array_merge($this->get, $this->post);
```

Note:

* POST values override GET if keys are the same

---

Example Usage in Controller:

```php
public function store(Request $request)
{
    $data = $request->all();

    $name = $request->input('name');
    $email = $request->input('email');

    return new Response("Saved {$name}");
}
```

---

Accessing Uploaded Files:

```php
$request->files['avatar'];
```

---

Accessing Cookies:

```php
$request->cookies['session'];
```

---

Important Behavior:

* No automatic sanitization
* No validation
* No JSON body parsing
* No PUT/PATCH parsing
* Not immutable
* Snapshot taken once at capture

---

Not Supported:

* Raw JSON body (`php://input`)
* Method spoofing
* Header access helper
* IP helper
* AJAX detection