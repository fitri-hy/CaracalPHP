<?php
namespace Caracal\Core;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}

class Middleware
{
    protected array $middlewares = [];

    public function register(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }

    public function run(Request $request, callable $final): Response
    {
        $stack = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $middleware) => fn($req) => (new $middleware())->handle($req, $next),
            $final
        );
        return $stack($request);
    }
}