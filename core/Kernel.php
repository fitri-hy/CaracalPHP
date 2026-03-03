<?php
namespace Caracal\Core;

class Kernel
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(): void
    {
        $plugins = $this->app->plugins();

        try {
            $plugins->trigger('before_request');

            $request  = Request::capture();
            $router   = new Router($this->app);

            $this->preloadCache($router);

            $plugins->trigger('before_dispatch', $request);

            $response = $router->dispatch($request);

            $plugins->trigger('after_dispatch', $response);

            if (!$response instanceof Response) {
                $response = new Response((string) $response);
            }

            $plugins->trigger('response_ready', $response);

        } catch (\Throwable $e) {

            $plugins->trigger('on_error', $e);

            $response = ErrorHandler::handle($e);

            if (!$response instanceof Response) {
                $response = new Response((string) $response, 500);
            }
        }

        $response->send();

        $plugins->trigger('after_response', $response);
    }

    protected function preloadCache(Router $router): void
    {
        $cache = $this->app->cache();

        if (!$cache->get('routes')) {
            $routes = $router->loadRoutes();
            $cache->set('routes', $routes, $cache->getDefaultTTL());
        }

        if (!$cache->get('layout')) {
            $layoutPath = $this->app->path('app/Modules/layout.view.php');
            if (is_file($layoutPath)) {
                $cache->set('layout', file_get_contents($layoutPath), $cache->getDefaultTTL());
            }
        }

        if (!$cache->get('middleware')) {
            $middlewares = [];
            $cache->set('middleware', $middlewares, $cache->getDefaultTTL());
        }

        if (!$cache->get('plugins')) {
            $plugins = [];
            $cache->set('plugins', $plugins, $cache->getDefaultTTL());
        }

        if (!$cache->get('events')) {
            $events = [];
            $cache->set('events', $events, $cache->getDefaultTTL());
        }
    }
}
