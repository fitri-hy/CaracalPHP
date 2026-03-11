<?php
namespace Caracal\Core;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use function FastRoute\cachedDispatcher;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class Router
{
    protected Application $app;

    protected array $routes = [];
    protected array $namedRoutes = [];
    protected array $groupStack = [];

    protected Cache $cache;

    protected string $cacheKey = 'routes';

    protected ?Dispatcher $dispatcher = null;

    public function __construct(Application $app)
    {
        $this->app   = $app;
        $this->cache = $app->cache();

        $this->load();
    }

    public function add(string $method, string $path, string $handler, array $options = []): void
    {
        $methods = explode('|', strtoupper($method));

        $route = [
            'methods'    => $methods,
            'path'       => $this->currentGroupPrefix() . $path,
            'handler'    => $handler,
            'middleware' => array_merge(
                $this->currentGroupMiddleware(),
                $options['middleware'] ?? []
            ),
            'name'       => $options['name'] ?? null
        ];

        $this->routes[] = $route;

        if ($route['name']) {
            $this->namedRoutes[$route['name']] = $route['path'];
        }
    }

    public function get(string $path, string $handler, array $options = []): void
    {
        $this->add('GET', $path, $handler, $options);
    }

    public function post(string $path, string $handler, array $options = []): void
    {
        $this->add('POST', $path, $handler, $options);
    }

    public function put(string $path, string $handler, array $options = []): void
    {
        $this->add('PUT', $path, $handler, $options);
    }

    public function delete(string $path, string $handler, array $options = []): void
    {
        $this->add('DELETE', $path, $handler, $options);
    }

    public function group(array $options, callable $callback): void
    {
        $this->groupStack[] = $options;

        $callback($this);

        array_pop($this->groupStack);
    }

    protected function currentGroupPrefix(): string
    {
        $prefixes = array_map(fn($g) => $g['prefix'] ?? '', $this->groupStack);

        return implode('', $prefixes);
    }

    protected function currentGroupMiddleware(): array
    {
        $middlewares = array_map(fn($g) => $g['middleware'] ?? [], $this->groupStack);

        return $middlewares ? array_merge(...$middlewares) : [];
    }

    protected function load(): void
    {
        $cached = $this->cache->get($this->cacheKey);

        if (is_array($cached)) {
            $this->routes      = $cached['routes'] ?? [];
            $this->namedRoutes = $cached['named'] ?? [];
            return;
        }

        foreach (glob($this->app->path('app/Modules/*/Routes/*.php')) as $file) {

            $routes = include $file;

            if (is_array($routes)) {

                foreach ($routes as $route) {

                    $method  = $route[0] ?? null;
                    $path    = $route[1] ?? null;
                    $handler = $route[2] ?? null;
                    $options = $route[3] ?? [];

                    if ($method && $path && $handler) {
                        $this->add($method, $path, $handler, $options);
                    }
                }
            }
        }

        $this->cache->set($this->cacheKey, [
            'routes' => $this->routes,
            'named'  => $this->namedRoutes
        ]);
    }

	protected function dispatcher(): Dispatcher
	{
		if ($this->dispatcher) {
			return $this->dispatcher;
		}

		$config = $this->app->config();
		$cacheEnabled = $config->get('cache.enabled', false);

		if (!$cacheEnabled) {

			$this->dispatcher = \FastRoute\simpleDispatcher(
				function (RouteCollector $r) {

					foreach ($this->routes as $route) {

						foreach ($route['methods'] as $method) {

							$r->addRoute($method, $route['path'], $route);

						}
					}

				}
			);

			return $this->dispatcher;
		}

		$this->dispatcher = cachedDispatcher(
			function (RouteCollector $r) {

				foreach ($this->routes as $route) {

					foreach ($route['methods'] as $method) {

						$r->addRoute($method, $route['path'], $route);

					}
				}

			},
			[
				'cacheFile' => $this->app->path('storage/cache/routes.php')
			]
		);

		return $this->dispatcher;
	}

    public function dispatch(Request $req): Response
    {
        $uri = $req->uri();

        $base = dirname($_SERVER['SCRIPT_NAME']);

        if ($base !== '/' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = $uri ?: '/';

        $info = $this->dispatcher()->dispatch($req->method(), $uri);

        try {

            switch ($info[0]) {

                case Dispatcher::NOT_FOUND:
                    return ErrorHandler::notFound();

                case Dispatcher::METHOD_NOT_ALLOWED:
                    return new Response('405 Method Not Allowed', 405);

                case Dispatcher::FOUND:
                    $route = $info[1];
                    $vars  = $info[2];

                    return $this->runRoute($route, $req, $vars);

            }

        } catch (Throwable $e) {

            return ErrorHandler::handle($e);

        }
    }

    protected function runRoute(array $route, Request $req, array $vars): Response
    {
        [$controller, $method] = explode('@', $route['handler']);

        $instance = $this->resolve($controller);

        $final = function () use ($instance, $method, $vars) {

            $result = $this->invoke($instance, $method, $vars);

            return $this->normalizeResponse($result);

        };

        if (!empty($route['middleware'])) {

            $runner = new Middleware();

            $runner->register($route['middleware']);

            return $runner->run($req, $final);

        }

        return $final();
    }

    protected function resolve(string $class)
    {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {

            $type = $param->getType();

            if ($type && !$type->isBuiltin()) {

                $dependencies[] = $this->resolve($type->getName());

            } else {

                $dependencies[] = null;

            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    protected function invoke(object $instance, string $method, array $vars)
    {
        $reflection = new ReflectionMethod($instance, $method);

        $args = [];

        foreach ($reflection->getParameters() as $param) {

            $type = $param->getType();

            if ($type && $type->getName() === Request::class) {

                $args[] = Request::capture();

            } elseif ($type && !$type->isBuiltin()) {

                $args[] = $this->resolve($type->getName());

            } elseif (isset($vars[$param->getName()])) {

                $args[] = $vars[$param->getName()];

            } else {

                $args[] = null;

            }
        }

        return $reflection->invokeArgs($instance, $args);
    }

    protected function normalizeResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        return new Response((string) $result);
    }

    public function route(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $path = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }

        return $path;
    }

    public function loadRoutes(): array
    {
        return $this->routes;
    }
}