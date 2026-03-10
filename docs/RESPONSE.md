# CaracalPHP – Response Documentation

Class:

```php id="k3x8fp"
Caracal\Core\Response
```

`Response` represents an HTTP response in CaracalPHP.

This class is responsible for:

* Storing response content
* Setting HTTP status codes
* Managing headers
* Sending output to the browser

Used by:

* Controllers
* Kernel
* ErrorHandler
* Middleware

---

Purpose of the Response class:

* Standardize HTTP output
* Support HTML responses
* Support JSON responses
* Support redirects

---

Internal Properties:

```php id="s4hl8c"
protected string $content;
protected int $status;
protected array $headers;
```

| Property | Purpose            |
| -------- | ------------------ |
| $content | Response body      |
| $status  | HTTP status code   |
| $headers | Additional headers |

---

Constructor:

```php id="y8j2nb"
public function __construct(
    string $content = '',
    int $status = 200,
    array $headers = []
)
```

Examples:

```php id="q7m1sv"
return new Response('<h1>Hello</h1>', 200);
```

With custom headers:

```php id="cmg4vr"
return new Response(
    'Created',
    201,
    ['X-Custom-Header' => 'Value']
);
```

---

Method `send`:

```php id="5ztg9a"
public function send(): void
```

Steps:

1. Set HTTP status
2. Send all headers
3. Output the content

Implementation:

```php id="g2sd7k"
http_response_code($this->status);

foreach ($this->headers as $key => $value) {
    header("$key: $value");
}

echo $this->content;
```

Typically called by the Kernel:

```php id="o6j3mw"
$response->send();
```

---

Static Method `json`:

```php id="3f8lqk"
public static function json(
    mixed $data,
    int $status = 200,
    array $headers = []
): self
```

Used to easily create a JSON response.

Features:

* Automatically sets `Content-Type: application/json`
* Pretty prints JSON
* Unicode characters are not escaped

Examples:

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

With custom status:

```php id="mt2q8l"
return Response::json(
    ['error' => 'Unauthorized'],
    401
);
```

With additional headers:

```php id="w1k8sr"
return Response::json(
    ['ok' => true],
    200,
    ['X-API-Version' => '1.0']
);
```

---

Static Method `redirect`:

```php id="p6y4nd"
public static function redirect(string $url, int $status = 302): self
```

Used to redirect to another URL.

Example:

```php id="j9m2tr"
return Response::redirect('/login');
```

Equivalent HTTP response:

```http id="u5b8ye"
HTTP/1.1 302 Found
Location: /login
```

With status 301:

```php id="l8x2sv"
return Response::redirect('/new-url', 301);
```

---

Usage in Controller:

HTML response:

```php id="c2k9mv"
return new Response('<h1>Dashboard</h1>');
```

JSON API response:

```php id="n4q7zx"
return Response::json([
    'user' => $user
]);
```

Redirect after submit:

```php id="e7d2lk"
return Response::redirect('/dashboard');
```