# CaracalPHP – Erro rHandler Documentation

Class

```php
Caracal\Core\ErrorHandler
```

`ErrorHandler` provides centralized error and exception handling for Caracal applications. It is used to display structured error pages, manage HTTP status codes, and provide debugging information during development.

---

## Handling Global Errors (HTTP 500)

Use `ErrorHandler::handle()` to catch exceptions in the application.

```php
use Caracal\Core\ErrorHandler;

try {
    // Execute controller or application logic
    $controller = new App\Modules\Home\Controllers\HomeController();
    $controller->index();
} catch (\Throwable $e) {
    return ErrorHandler::handle($e);
}
```

Result

Displays an HTML error page containing exception details.
If `APP_DEBUG=true`, a code snippet around the error line will be shown.
The HTTP status code is automatically set to **500**.

---

## Passing Controller and Method Information (Optional)

Controller and method information can be passed to provide additional context.

```php
return ErrorHandler::handle(
    $e,
    App\Modules\User\Controllers\UserController::class,
    'store'
);
```

Result

Displays a note if the method `store()` does not exist.
Shows the code snippet when debug mode is enabled.

---

## Using a Custom 404 Page

Use `ErrorHandler::notFound()` when a route cannot be resolved.

```php
use Caracal\Core\ErrorHandler;

return ErrorHandler::notFound();
```

### Custom 404 View Location

Create the following file.

```
app/Modules/Error/Views/404.view.php
```

Example content

```html
<h1>404 – Page Not Found</h1>
<p>The page you are looking for is not available.</p>
<a href="/">Back to Homepage</a>
```

Result

This page will be displayed whenever a route is not matched.
The HTTP status code is automatically set to **404**.
If the file does not exist, the system falls back to the default 404 page.

---

## Router Integration

Inside the router implementation

```php
$response = $router->dispatch($request);

if ($response->status() === 404) {
    return ErrorHandler::notFound();
}
```

With this integration, every unregistered URL will automatically display the 404 page.

---

## Debug Mode

Enable debug mode in the environment configuration.

```env
APP_DEBUG=true
```

Behavior

Displays a code snippet around the error location on the 500 error page.
Highlights the exact line where the error occurred.
Does not affect 404 responses.

---

## Usage Summary

| Scenario                   | Usage                                                   | HTTP Status |
| -------------------------- | ------------------------------------------------------- | ----------- |
| Global error or exception  | `ErrorHandler::handle($e)`                              | 500         |
| Controller or method error | `ErrorHandler::handle($e, Controller::class, 'method')` | 500         |
| Route not found            | `ErrorHandler::notFound()`                              | 404         |
| Custom 404 page            | Create `app/Modules/Error/Views/404.view.php`           | 404         |
| Debug mode enabled         | `.env → APP_DEBUG=true`                                 | —           |
