# CaracalPHP – Validation Documentation

Class:

```php
Caracal\Core\Validation
```

`Validation` is a **lightweight wrapper** for [Respect\Validation](https://respect-validation.readthedocs.io/) that makes **input data validation** simple and clean.

It allows you to:

* Validate arrays of data with **rules per field**
* Collect **all error messages** in an array
* Easily integrate into forms, API requests, or CaracalPHP modules

> Simplifies validation without writing repetitive boilerplate.

---

## Initialization

```php
use Caracal\Core\Validation;

$validator = new Validation();
```

No additional configuration is needed—just instantiate the object.

---

## Basic Usage

### Define Rules

Use `Respect\Validation\Validator` to define rules:

```php
use Respect\Validation\Validator as v;

$rules = [
    'name'  => v::stringType()->notEmpty()->length(3, 50),
    'email' => v::email()->notEmpty(),
    'age'   => v::optional(v::intType()->min(18))
];
```

### Validate Data

```php
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25
];

if ($validator->validate($data, $rules)) {
    echo "Data is valid!";
} else {
    print_r($validator->errors());
}
```

If validation fails, `errors()` returns an array:

```php
[
    'name' => [
        "Name must have a length between 3 and 50"
    ],
    'email' => [
        "Email must be valid"
    ]
]
```

---

## Handling Optional Fields

Use `v::optional()` for fields that **can be empty**:

```php
$rules = [
    'phone' => v::optional(v::phone())
];
```

If `phone` is empty, the validation will be skipped.

---

## Advanced Example

Form validation example:

```php
$data = [
    'username' => 'johndoe',
    'password' => 'secret123',
    'email'    => 'invalid-email'
];

$rules = [
    'username' => v::alnum()->noWhitespace()->length(3, 20),
    'password' => v::stringType()->length(6, 50),
    'email'    => v::email()
];

if (!$validator->validate($data, $rules)) {
    foreach ($validator->errors() as $field => $messages) {
        foreach ($messages as $msg) {
            echo "<p>$field error: $msg</p>";
        }
    }
}
```

> All errors are collected at once, so they can be displayed in a form or returned in an API response.

---

## Notes

* `Validation` is a **lightweight wrapper** around Respect\Validation
* Error messages automatically capitalize the field name
* Suitable for **web forms**, **API requests**, and **module input validation**
* Supports all standard Respect\Validation validators such as:

  * `v::email()`, `v::intType()`, `v::length()`, `v::alnum()`, `v::phone()`, etc.
