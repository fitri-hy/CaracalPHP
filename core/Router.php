<?php
namespace Caracal\Core;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Throwable;

class Router
{
    protected Application $app;
    protected array $routes = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->load();
    }

    protected function load(): void
    {
        foreach (glob($this->app->path('app/Modules/*/Routes/*.php')) as $file) {
            $moduleRoutes = include $file;
            if (is_array($moduleRoutes)) {
                $this->routes = array_merge($this->routes, $moduleRoutes);
            }
        }
    }

    public function loadRoutes(): array
    {
        return $this->routes;
    }

    public function dispatch(Request $req): Response
    {
        $dispatcher = simpleDispatcher(function(RouteCollector $r) {
            foreach ($this->routes as $route) {
                [$method, $path, $handler] = $route;
                $r->addRoute($method, $path, $handler);
            }
        });

        $info = $dispatcher->dispatch($req->method(), $req->uri());

        switch ($info[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                return new Response('404 Not Found', 404);
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return new Response('405 Method Not Allowed', 405);
        }

        try {
            [$controller, $method] = explode('@', $info[1]);
            $vars = $info[2];

            $instance = new $controller();

            $result = $instance->{$method}(...array_values($vars));

            return $this->normalizeResponse($result, $req);

        } catch (Throwable $e) {
            return ErrorHandler::handle($e, $controller ?? null, $method ?? null);
        }
    }

    protected function normalizeResponse(mixed $result, Request $req): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        return new Response((string)$result);
    }
}