# CaracalPHP – Exception Documentation

This file defines the custom exception classes used by the framework.

```php
Caracal\Core\CaracalException
Caracal\Core\NotFoundException
Caracal\Core\ValidationException
Caracal\Core\CSRFException
```

All exceptions extend the base class

```php
Caracal\Core\CaracalException
```

---

## CaracalException

Primary exception class

```php
class CaracalException extends \Exception
```

Additional features compared to the default PHP `Exception`.

Supports a `context` array for additional error data
Provides a `toArray()` method

---

## Constructor

```php
public function __construct(
    string $message = "",
    int $code = 0,
    array $context = [],
    Throwable $previous = null
)
```

Additional parameters compared to the standard exception.

| Parameter   | Description                          |
| ----------- | ------------------------------------ |
| `$context`  | Additional data related to the error |
| `$previous` | Previous exception for chaining      |

---

## Example Usage

```php
use Caracal\Core\CaracalException;

throw new CaracalException(
    'Something went wrong',
    500,
    ['user_id' => 10]
);
```

---

## Retrieving Context

Use the following method.

```php
$e->getContext();
```

Example

```php
try {
    throw new CaracalException('Error', 500, ['id' => 1]);
} catch (CaracalException $e) {
    $context = $e->getContext();
}
```

---

## Converting to an Array

Method

```php
$e->toArray();
```

Return structure

```php
[
    'message' => string,
    'code'    => int,
    'file'    => string,
    'line'    => int,
    'context' => array,
    'trace'   => string,
]
```

Example

```php
try {
    throw new CaracalException('Database error', 500, ['table' => 'users']);
} catch (CaracalException $e) {
    print_r($e->toArray());
}
```

This format is useful for

JSON API responses
Application logging
Debugging output

---

## Derived Exceptions

### NotFoundException

```php
class NotFoundException extends CaracalException {}
```

Used for cases such as

Routes that cannot be resolved
Missing resources
Unavailable data

Example

```php
use Caracal\Core\NotFoundException;

throw new NotFoundException('User not found');
```

---

### ValidationException

```php
class ValidationException extends CaracalException {}
```

Used when

Form validation fails
Input does not match required rules

Example

```php
use Caracal\Core\ValidationException;

throw new ValidationException(
    'Validation failed',
    422,
    ['field' => 'email']
);
```

---

### CSRFException

```php
class CSRFException extends CaracalException {}
```

Used when

The CSRF token is invalid
The CSRF token is missing

Example

```php
use Caracal\Core\CSRFException;

throw new CSRFException('Invalid CSRF token');
```

---

## Important Behavior

All derived exceptions inherit the `context` feature
`toArray()` always includes the stack trace as a string
The default value of `context` is an empty array
The class remains fully compatible with `Throwable`

---

## Difference from Standard Exception

| Feature       | Exception | CaracalException |
| ------------- | --------- | ---------------- |
| Message       | Yes       | Yes              |
| Code          | Yes       | Yes              |
| File and Line | Yes       | Yes              |
| Context       | No        | Yes              |
| toArray()     | No        | Yes              |
