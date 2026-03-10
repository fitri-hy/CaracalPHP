# CaracalPHP – Controller Documentation

Class

```php
Caracal\Core\Controller
```

All module controllers should extend this class.

The controller provides direct access to the following components.

Request
View renderer
Asset helper
Config
Cache

The controller is designed to keep module controllers simple and clean.

---

## Available Properties

When extending the controller, the following properties are automatically available.

```php
protected Request $request
protected View $view
protected Asset $asset
protected Application $app
```

These properties are initialized in the constructor.

```php
$this->app     = Application::getInstance();
$this->request = Request::capture();
$this->view    = new View();
$this->asset   = new Asset();
```

---

## Using Controllers in Modules

Controller location

```
app/Modules/{ModuleName}/Controllers/
```

Example controller

```php
namespace App\Modules\Home\Controllers;

use Caracal\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->view(
            'Home/Views/home.view.php',
            [
                'title' => 'Homepage'
            ]
        );
    }
}
```

---

## Rendering Views

Primary method used to render HTML pages

```php
view(string $template, array $data = [], bool $layout = true): string
```

Parameters

| Parameter | Description                         |
| --------- | ----------------------------------- |
| template  | View path relative to `app/Modules` |
| data      | Data passed to the template         |
| layout    | Whether to use the global layout    |

Example

```php
return $this->view(
    'Blog/Views/post.view.php',
    [
        'title' => 'My Post'
    ]
);
```

Without layout

```php
return $this->view(
    'Blog/Views/card.view.php',
    $data,
    false
);
```

---

## Asset Helper

The controller automatically injects the `$asset` object into views.

It can be used directly in templates.

```php
<link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
<script src="<?= $asset->url('js/app.js') ?>"></script>
```

---

## Accessing Request Input

The controller provides a helper for retrieving request input.

```php
input(string $key, mixed $default = null)
```

Example

```php
$name = $this->input('name');
```

This reads input from

GET parameters
POST parameters
JSON request body

depending on the implementation of `Request`.

---

## Accessing Configuration

Configuration values can be accessed directly from the controller.

```php
config(string $key, mixed $default = null)
```

Example

```php
$debug = $this->config('app.debug');
```

---

## Using Cache

The controller provides access to the cache system through a helper method.

```php
$cache = $this->cache();
```

Example usage

```php
$posts = $this->cache()->remember('home.posts', function () {
    return (new PostModel())->getAll();
});
```

The cache driver follows the configuration defined in the `.env` file.

---

## JSON Response

For API endpoints, use the following method.

```php
$this->json([
    'success' => true
]);
```

Example API controller

```php
public function api(): void
{
    $this->json([
        'success' => true,
        'message' => 'Welcome to Caracal API'
    ]);
}
```

This method will

Set the HTTP status code
Send the `Content-Type: application/json` header
Encode the data as JSON

---

## Redirect

To redirect to another page

```php
$this->redirect('/dashboard');
```

This method sends the following header

```
Location: /dashboard
```

and stops script execution.

---

## Method Summary

| Method     | Description                           |
| ---------- | ------------------------------------- |
| view()     | Render an HTML template               |
| json()     | Return a JSON API response            |
| redirect() | Perform an HTTP redirect              |
| input()    | Retrieve request input                |
| config()   | Read configuration values             |
| cache()    | Access the application cache instance |
