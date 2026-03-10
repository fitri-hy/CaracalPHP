# CaracalPHP – CSRF Protection Documentation

Class

```php id="s3g9af"
Caracal\Core\CSRF
```

This class provides protection against **Cross-Site Request Forgery (CSRF)**.

It is used to generate tokens, inject tokens into forms, validate tokens during requests, and protect POST, PUT, and DELETE operations.

---

## CSRF Workflow in Caracal

The CSRF protection mechanism follows this process.

A token is generated using `random_bytes()`
The token is stored in the session
The token is sent through a hidden form input or the `X-CSRF-TOKEN` header
The token is validated when a request is received
Each token can be used only once

---

## Generating a Token

```php id="h14i9k"
$csrf = new CSRF();

$token = $csrf->generate();
```

The token is automatically stored in the session.

---

## Injecting the Token into a Form

The simplest way to include the token in a form

```php id="xsvx9u"
<?= (new \Caracal\Core\CSRF())->inputField(); ?>
```

Output

```html id="shak20"
<input type="hidden" name="_csrf" value="TOKEN">
```

---

## Example Form

```php id="18o0sx"
<form method="POST" action="/submit">

    <?= (new \Caracal\Core\CSRF())->inputField(); ?>

    <input type="text" name="name">

    <button type="submit">Submit</button>

</form>
```

---

## Manual Token Validation

```php id="h1jdtl"
$csrf = new CSRF();

$token = $_POST['_csrf'] ?? null;

if (!$csrf->validate($token)) {
    die('Invalid CSRF token');
}
```

---

## Automatic POST Validation

```php id="2l17s1"
$csrf->checkPost();
```

Example inside a controller

```php id="t0k3h5"
public function store()
{
    $csrf = new CSRF();

    if (!$csrf->checkPost()) {
        die('CSRF validation failed');
    }

    // continue processing
}
```

---

## AJAX or API Validation

Send the token through the following header.

```id="6lt91y"
X-CSRF-TOKEN
```

JavaScript example

```javascript id="gaxww3"
fetch('/api/post', {
  method: 'POST',
  headers: {
    'X-CSRF-TOKEN': token
  }
});
```

Server validation

```php id="2de1m7"
$csrf = new CSRF();

if (!$csrf->checkHeader()) {
    die('Invalid CSRF token');
}
```

---

## Retrieving the Token Manually

If the token needs to be injected into JavaScript

```php id="d0aw13"
$token = (new CSRF())->token();
```

Example

```php id="86r9mp"
<script>
const csrf = "<?= (new CSRF())->token() ?>";
</script>
```

---

## Clearing All Tokens

```php id="93d6pl"
$csrf->clear();
```

All CSRF tokens stored in the session will be removed.

---

## Security Behavior

CSRF tokens include the following security characteristics.

Generated using `random_bytes`
Stored in the session
Single-use tokens
Token expiration with a TTL of approximately 30 minutes
Support for multiple tokens for different forms

---

## Method Summary

| Method        | Description                   |
| ------------- | ----------------------------- |
| generate()    | Generate a CSRF token         |
| token()       | Alias of generate             |
| inputField()  | Generate a hidden input field |
| validate()    | Validate a CSRF token         |
| checkPost()   | Validate POST requests        |
| checkHeader() | Validate AJAX header tokens   |
| clear()       | Remove all stored tokens      |
