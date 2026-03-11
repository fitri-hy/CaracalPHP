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

# Features

* Module-based view rendering
* Global layout support
* View & layout caching
* Partial view inclusion
* Reusable view components
* Custom layout support
* Path security protection

---

# View Structure

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

# Initialization

```php
use Caracal\Core\View;

$view = new View();
```

Typical usage in a controller:

```php
$this->render('Welcome/Views/welcome.view.php');
```

---

# Rendering a View

```php
render(string $template, array $data = [], bool $useLayout = true): string
```

Parameters:

| Parameter | Description                                     |
| --------- | ----------------------------------------------- |
| template  | relative path from `app/Modules`                |
| data      | variables passed to the template                |
| useLayout | whether to wrap the view with the global layout |

Example:

```php
echo $view->render(
    'Welcome/Views/welcome.view.php',
    ['title' => 'Welcome', 'message' => 'Hello Caracal']
);
```

---

# Passing Data

Data passed from the controller becomes variables inside the template.

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

# Layout System

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

`{{content}}` is automatically replaced with the rendered view.

---

## Render Without Layout

```php
$view->render(
    'Welcome/Views/card.view.php',
    ['title' => 'Product'],
    false
);
```

Useful for:

* AJAX responses
* API HTML fragments
* reusable UI blocks

---

## Custom Layout

```php
$view->setLayout(APP_PATH . '/Modules/Admin/layout.view.php');
```

Get current layout:

```php
$layout = $view->getLayout();
```

---

# Partial Views

Partial views allow you to **include other view files inside a view**.

Method used:

```php
$this->partial(string $template, array $data = []);
```

Example:

```php
<?php $this->partial('Welcome/Views/header.view.php'); ?>
```

Passing data:

```php
<?php $this->partial(
    'Blog/Views/post.view.php',
    ['post' => $post]
); ?>
```

Notes:

* Partial views **do not use layouts**
* They render directly inside the current view
* Useful for headers, footers, or reusable UI pieces

---

# View Components

Components are **reusable UI elements built using partial views**.

CaracalPHP does not require a special component system — components are implemented using `partial()`.

---

## Recommended Component Structure

```
app
 └─ Modules
     ├─ Components
     │   ├─ navbar.view.php
     │   ├─ footer.view.php
     │   └─ card.view.php
     │
     └─ Welcome
         └─ Views
             └─ home.view.php
```

---

## Example Component

### Navbar Component

```
app/Modules/Components/navbar.view.php
```

```php
<nav class="bg-blue-600 text-white p-4">
    <h1><?= $title ?? 'Navbar' ?></h1>
</nav>
```

Use inside a view:

```php
<?php $this->partial(
    'Components/navbar.view.php',
    ['title' => 'Home']
); ?>
```

---

### Card Component

```
app/Modules/Components/card.view.php
```

```php
<div class="border rounded p-4 shadow">
    <h2><?= $title ?></h2>
    <p><?= $content ?></p>
</div>
```

Usage:

```php
<?php $this->partial('Components/card.view.php', [
    'title' => 'Product',
    'content' => 'Product description'
]); ?>
```

---

## Components in Layout

Components can also be used inside the layout:

```php
<?php $this->partial('Components/navbar.view.php'); ?>

<main>
    {{content}}
</main>

<?php $this->partial('Components/footer.view.php'); ?>
```

---

# View Caching

Activated via `.env`:

```
CACHE_ENABLED=true
```

Behavior:

* Caches rendered views
* Caches layout template
* Cache key example:

```
view_{md5(template + data_keys)}
```

Layout caching stores:

```
layout template only (HTML)
```

Benefits:

* Faster rendering
* Reduced template processing
* Reduced disk reads

---

# Rendering Process

1. Check if caching is enabled
2. Check cache for the rendered view
3. Render the view file
4. Load layout template
5. Replace `{{content}}` with rendered view
6. Save result to cache
7. Return HTML

---

# Security

The view system prevents **directory traversal attacks**.

Protection includes:

* `realpath()` validation
* path prefix checking
* restriction to `app/Modules`

Example blocked paths:

```
../../../.env
../../../config/database.php
```

---

# Error Handling

Possible exceptions:

| Error                   | Description                             |
| ----------------------- | --------------------------------------- |
| `Invalid view path`     | Attempt to access files outside Modules |
| `View {file} not found` | View file does not exist                |

Valid example:

```
Welcome/Views/welcome.view.php
```

---

# Controller Example

```php
public function index(): string
{
    return $this->render(
        'Welcome/Views/welcome.view.php',
        [
            'title' => 'Welcome',
            'message' => 'Welcome to Caracal Framework'
        ]
    );
}
```

---

# Summary

| Feature              | Supported |
| -------------------- | --------- |
| Module Views         | yes       |
| Global Layout        | yes       |
| Custom Layout        | yes       |
| Partial Views        | yes       |
| View Components      | yes       |
| View Cache           | yes       |
| Layout Cache         | yes       |
| Path Security        | yes       |
| Native PHP Templates | yes       |
