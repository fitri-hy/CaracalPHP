# CaracalPHP – Middleware Documentation

This file contains:

```php
Caracal\Core\MiddlewareInterface
Caracal\Core\Middleware
```

Middleware is used to:

* Process requests before reaching the controller
* Modify requests or responses
* Stop execution if necessary
* Create a chained execution pipeline

---

MiddlewareInterface:

```php
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
```

All middleware must implement the `handle` method:

```php
handle(Request $request, callable $next): Response
```

---

Parameters:

| Parameter | Purpose                          |
| --------- | -------------------------------- |
| $request  | The current Request object       |
| $next     | Closure to continue the pipeline |

---

Example middleware:

```php
use Caracal\Core\MiddlewareInterface;
use Caracal\Core\Request;
use Caracal\Core\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!isset($_SESSION['user'])) {
            return new Response('Unauthorized', 401);
        }

        return $next($request);
    }
}
```

---

Middleware class (Pipeline Executor):

```php
class Middleware
```

This class is responsible for:

* Storing the list of middleware
* Running middleware in a chained pipeline

---

Method `register`:

```php
public function register(array $middlewares): void
```

Used to register middleware. Example:

```php
$middleware = new Middleware();

$middleware->register([
    AuthMiddleware::class,
    LogMiddleware::class,
]);
```

The array must contain middleware class names.

---

Method `run`:

```php
public function run(Request $request, callable $final): Response
```

This method executes all registered middleware in a chain.

---

Internal workflow:

```php
$stack = array_reduce(
    array_reverse($this->middlewares),
    fn($next, $middleware) =>
        fn($req) => (new $middleware())->handle($req, $next),
    $final
);
```

Explanation:

* Middleware are reversed in order
* Nested closures are created
* Each middleware receives:

  * Request
  * Next closure
* The last middleware calls `$final`

---

Pipeline illustration:

If the middleware array is:

```php
[
    A::class,
    B::class,
    C::class
]
```

The execution becomes:

```
A → B → C → FINAL
```

---

Complete usage example:

```php
use Caracal\Core\Middleware;
use Caracal\Core\Request;
use Caracal\Core\Response;

$middleware = new Middleware();

$middleware->register([
    AuthMiddleware::class,
]);

$response = $middleware->run(
    Request::capture(),
    function ($request) {
        return new Response('Controller executed');
    }
);

$response->send();
```

---

Execution pattern:

* Each middleware must either:

  * Call `$next($request)` to continue
  * Or return a `Response` to stop the pipeline

---

Stopping execution:

If middleware does not call `$next`, the pipeline stops. Example:

```php
return new Response('Forbidden', 403);
```

The controller will not be executed.