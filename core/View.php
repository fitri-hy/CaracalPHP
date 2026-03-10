<?php

namespace Caracal\Core;

class View
{
    protected string $base;
    protected string $layout;

    public function __construct()
    {
        $root = realpath(dirname(__DIR__));

        $this->base   = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Modules';
        $this->layout = $this->base . DIRECTORY_SEPARATOR . 'layout.view.php';
    }

    public function render(string $template, array $data = [], bool $useLayout = true): string
    {
        $app    = Application::getInstance();
        $cache  = $app->cache();
        $config = $app->config();

        $cacheEnabled = $config->get('cache.enabled', false);

        $cacheKey = 'view_' . md5($template . serialize(array_keys($data)));

        if ($cacheEnabled) {
            $cached = $cache->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $content = $this->renderFile($template, $data);

        if ($useLayout && file_exists($this->layout)) {

            $layout = null;

            if ($cacheEnabled) {
                $layout = $cache->get('layout_template');
            }

            if ($layout === null) {
                $layout = file_get_contents($this->layout);

                if ($cacheEnabled) {
                    $cache->set('layout_template', $layout);
                }
            }

            extract($data);

            ob_start();
            eval('?>' . str_replace('{{content}}', $content, $layout));
            $content = ob_get_clean();
        }

        if ($cacheEnabled) {
            $cache->set($cacheKey, $content);
        }

        return $content;
    }

    protected function renderFile(string $template, array $data): string
    {
        extract($data);

        $file = str_starts_with($template, $this->base)
            ? $template
            : $this->base . DIRECTORY_SEPARATOR . $template;

        $file = realpath($file);

        if (!$file || !str_starts_with($file, $this->base)) {
            throw new \Exception("Invalid view path");
        }

        if (!file_exists($file)) {
            throw new \Exception("View {$file} not found");
        }

        ob_start();

        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function partial(string $template, array $data = []): void
    {
        echo $this->renderFile($template, $data);
    }
}