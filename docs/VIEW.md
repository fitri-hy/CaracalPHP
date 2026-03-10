# CaracalPHP – View Documentation

Class:

```php
Caracal\Core\View
```

`View` is the **built-in template rendering engine** for CaracalPHP, using **native PHP templates**.

It is designed to be:

* Lightweight
* Flexible
* Secure
* Fast (with caching)

---

## Features

* Module-based view rendering
* Global layout support
* View & layout caching
* Partial view inclusion
* Custom layout support
* Path security protection

---

## View Structure

All views reside in:

```
app/Modules
```

Example:

```
app
 └─ Modules
     └─ Welcome
         └─ Views
             └─ welcome.view.php
```

Global layout:

```
app/Modules/layout.view.php
```

---

## Initialization

```php
use Caracal\Core\View;

$view = new View();
```

Typical usage in a controller:

```php
$this->render('Welcome/Views/welcome.view.php');
```

---

## Rendering a View

```php
render(string $template, array $data = [], bool $useLayout = true): string
```

* `template` – relative path from `app/Modules`
* `data` – variables passed to the template
* `useLayout` – whether to wrap with the global layout

Example:

```php
echo $view->render(
    'Welcome/Views/welcome.view.php',
    ['title' => 'Welcome', 'message' => 'Hello Caracal']
);
```

---

## Passing Data

Data passed from the controller becomes variables in the template:

Controller:

```php
$view->render('Welcome/Views/dashboard.view.php', [
    'username' => 'John',
    'notifications' => 5
]);
```

View:

```php
<h1>Hello <?= $username ?></h1>
<p>You have <?= $notifications ?> notifications</p>
```

> Internally uses `extract($data)`.

---

## Layout System

Default layout:

```
app/Modules/layout.view.php
```

Example layout:

```php
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Caracal' ?></title>
</head>
<body>

{{content}}

</body>
</html>
```

`{{content}}` is replaced with the rendered view.

---

### Render Without Layout

```php
$view->render(
    'Welcome/Views/card.view.php',
    ['title' => 'Product'],
    false
);
```

*Useful for partial templates, AJAX responses, or widgets.*

---

### Custom Layout

```php
$view->setLayout(APP_PATH . '/Modules/Admin/layout.view.php');
$layout = $view->getLayout();
```

---

## Partial Views

Include other views inside a view:

```php
<?php $this->partial('Welcome/Views/header.view.php'); ?>

<?php $this->partial(
    'Blog/Views/post.view.php',
    ['post' => $post]
); ?>
```

*Partial views do not use the layout.*

---

## View Caching

Activated via `.env`:

```
CACHE_ENABLED=true
```

* Caches rendered views and layout templates
* Cache key example: `view_{md5(template + data_keys)}`
* Layout cache stores **template HTML only**, not rendered content

Benefits:

* Faster page rendering
* Reduced template parsing
* Reduced file I/O

---

## Rendering Process

1. Check if caching is enabled
2. Check cache for the view
3. Render view file
4. Load layout template
5. Replace `{{content}}` with rendered view
6. Save result to cache
7. Return HTML

---

## Security

* Protects against **directory traversal attacks**
* Validates paths using `realpath()`
* Ensures views remain inside `app/Modules`

Invalid paths like `../../../.env` are blocked.

---

## Error Handling

* File not found → `Exception: View {file} not found`
* Invalid path → `Exception: Invalid view path`

Ensure paths follow:

```
Module/Views/view.view.php
```

Example: `Welcome/Views/welcome.view.php`.

---

## Controller Example

```php
public function index(): string
{
    return $this->render(
        'Welcome/Views/welcome.view.php',
        ['title' => 'Welcome', 'message' => 'Welcome to Caracal Framework']
    );
}
```

---

## Summary

| Feature             | Supported |
| ------------------- | --------- |
| Module Views        | yes       |
| Global Layout       | yes       |
| Custom Layout       | yes       |
| Partial View        | yes       |
| View Cache          | yes       |
| Layout Cache        | yes       |
| Path Security       | yes       |
| Native PHP Template | yes       |
