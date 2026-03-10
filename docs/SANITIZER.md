# CaracalPHP – Sanitizer Documentation

Class:

```php
Caracal\Core\Sanitizer
```

`Sanitizer` is used to clean HTML input to protect against attacks like **XSS (Cross-Site Scripting)**.

It uses **HTMLPurifier** to ensure the output is safe while preserving valid HTML structure.

---

Purpose of Sanitizer:

* Clean user input
* Secure rich text editors
* Allow iframes only from whitelisted domains
* Clean array input, e.g., `$_POST`

---

Basic Usage:

Clean a string:

```php
use Caracal\Core\Sanitizer;

$sanitizer = new Sanitizer();

$clean = $sanitizer->clean($input);
```

Method:

```php
public function clean(string $input): string
```

Behavior:

* Removes malicious scripts
* Removes dangerous attributes
* Ensures safe HTML output

---

Cleaning Array Input:

If input is in an array:

```php
$cleanData = $sanitizer->cleanArray($_POST);
```

Method:

```php
public function cleanArray(array $data): array
```

Behavior:

* Cleans every string value
* Leaves non-string values untouched
* Does **not** recursively clean nested arrays (as currently implemented)

Example:

Input:

```php
[
    'name' => '<script>alert(1)</script>',
    'age' => 25
]
```

Output:

```php
[
    'name' => '',
    'age' => 25
]
```

---

Allowing Specific Iframe Domains:

By default, iframes are restricted. You can allow specific domains via the constructor:

```php
$sanitizer = new Sanitizer([
    'www.youtube.com',
    'youtube.com'
]);
```

Example input:

```html
<iframe src="https://www.youtube.com/embed/xxxx"></iframe>
```

If the domain is not allowed → the iframe will be removed.

---

Constructor Signature:

```php
public function __construct(array $allowedIframeDomains = [])
```

Parameters:

| Parameter            | Type  | Description                    |
| -------------------- | ----- | ------------------------------ |
| allowedIframeDomains | array | List of allowed iframe domains |

If empty → iframes remain in safe mode without additional whitelist domains.

---

Controller Example:

```php
use Caracal\Core\Sanitizer;

class PostController
{
    public function store()
    {
        $sanitizer = new Sanitizer([
            'www.youtube.com'
        ]);

        $data = $sanitizer->cleanArray($_POST);

        // Save to database
    }
}
```

---

When to Use Sanitizer:

* Storing user comments
* Storing blog articles
* Storing content from WYSIWYG editors
* Receiving HTML from forms

Do **not** use for:

* Numeric data
* Internal system data
* Non-user-provided input

---

Feature Summary:

| Feature                  | Status |
| ------------------------ | ------ |
| HTML Cleaning            | ✅      |
| Script Removal           | ✅      |
| Safe Iframe Mode         | ✅      |
| Domain Whitelist         | ✅      |
| Array Cleaning           | ✅      |
| HTMLPurifier Integration | ✅      |