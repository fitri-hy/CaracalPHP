# CaracalPHP – Kernel Documentation

Class

```php
Caracal\Core\Kernel
```

The **Kernel** is the main **request lifecycle entry point** in CaracalPHP.

It is responsible for:

* Executing plugin hooks
* Handling incoming requests
* Dispatching routes
* Handling errors
* Sending responses
* Preloading system cache

---

# Role of the Kernel

The Kernel controls the following execution flow.

```
before_request
→ preloadCache
→ before_dispatch
→ router dispatch
→ after_dispatch
→ response_ready
→ send response
→ after_response
```

If an error occurs:

```
on_error
→ ErrorHandler::handle()
→ send response 500
```

---

# Constructor

```php
public function __construct(Application $app)
```

The Kernel requires an **Application instance**.

Typically called from the front controller (for example `public/index.php`).

```php
$app = new Application();
$kernel = new Kernel($app);
$kernel->handle();
```

---

# `handle()` Method

```php
public function handle(): void
```

This method runs the entire **request lifecycle**.

---

## Detailed Execution Order

### 1. Get Plugin Manager

```php
$plugins = $this->app->plugins();
```

---

### 2. Trigger `before_request`

```php
$plugins->trigger('before_request');
```

Used for tasks such as:

* Early request logging
* Environment setup
* Custom boot logic

---

### 3. Capture Request

```php
$request = Request::capture();
```

---

### 4. Initialize Router

```php
$router = new Router($this->app);
```

---

### 5. Preload Cache

```php
$this->preloadCache($router);
```

(See the **preloadCache** section below.)

---

### 6. Trigger `before_dispatch`

```php
$plugins->trigger('before_dispatch', $request);
```

---

### 7. Dispatch Route

```php
$response = $router->dispatch($request);
```

---

### 8. Trigger `after_dispatch`

```php
$plugins->trigger('after_dispatch', $response);
```

---

### 9. Ensure Response Instance

If the controller does not return a `Response` object:

```php
$response = new Response((string) $response);
```

---

### 10. Trigger `response_ready`

```php
$plugins->trigger('response_ready', $response);
```

---

# Error Handling

If an exception occurs:

```php
catch (\Throwable $e)
```

Execution flow:

1. Trigger `on_error`
2. Execute

```php
$response = ErrorHandler::handle($e);
```

3. Ensure the result is a `Response` instance
4. Send HTTP **500 status** if necessary

---

# Sending the Response

```php
$response->send();
```

After the response is sent:

```php
$plugins->trigger('after_response', $response);
```

---

# `preloadCache()`

Method

```php
protected function preloadCache(Router $router): void
```

This method preloads system cache if it does not already exist.

---

## Cached Components

### Routes

```php
$cache->get('routes')
```

If not available:

```php
$routes = $router->loadRoutes();
$cache->set('routes', $routes);
```

---

### Layout

File checked at:

```
app/Modules/layout.view.php
```

If present → stored as a cached string.

---

### Middleware

Stored as an empty array if not already cached.

---

### Plugins

Stored as an empty array if not already cached.

---

### Events

Stored as an empty array if not already cached.

---

# Lifecycle Summary

| Stage               | Trigger         |
| ------------------- | --------------- |
| Start of request    | before_request  |
| Before dispatch     | before_dispatch |
| After dispatch      | after_dispatch  |
| Response ready      | response_ready  |
| Error occurred      | on_error        |
| After response sent | after_response  |
