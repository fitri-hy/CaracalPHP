<?php

namespace Caracal\Core;

class Controller
{
    protected Request $request;
    protected View $view;
    protected Asset $asset;
    protected Application $app;

    public function __construct()
    {
        $this->app     = Application::getInstance();
        $this->request = Request::capture();
        $this->view    = new View();
        $this->asset   = new Asset();
    }

    protected function view(string $template, array $data = [], bool $layout = true): string
    {
        $data['asset'] = $this->asset;

        return $this->view->render($template, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);

        header('Content-Type: application/json');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    protected function config(string $key, mixed $default = null): mixed
    {
        return $this->app->config()->get($key, $default);
    }

    protected function cache(): Cache
    {
        return $this->app->cache();
    }
}