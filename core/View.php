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
		$cache = Application::getInstance()->cache();

		$key = 'view_' . md5($template . serialize($data));

		$cached = $cache->get($key);
		if ($cached !== null) {
			return $cached;
		}

		$content = $this->renderFile($template, $data);

		if ($useLayout) {
			$layoutContent = $cache->get('layout_global');
			if ($layoutContent === null) {
				$layoutContent = $this->renderFile($this->layout, array_merge($data, ['content' => $content]));
				$cache->set('layout_global', $layoutContent);
			}
			$content = str_replace('{{content}}', $content, $layoutContent);
		}

		$cache->set($key, $content);

		return $content;
	}

	protected function renderFile(string $template, array $data): string
	{
		extract($data);

		$file = str_starts_with($template, $this->base)
			? $template
			: $this->base . DIRECTORY_SEPARATOR . $template;

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
}