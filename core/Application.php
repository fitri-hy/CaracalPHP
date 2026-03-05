<?php

namespace Caracal\Core;

class Application
{
    protected static ?self $instance = null;

    public Config $config;
    public Kernel $kernel;
    public ?Database $database = null;
    public string $basePath;

    protected ?Cache $cache = null;
    protected Plugin $plugin;

    private function __construct()
    {
        $this->basePath = dirname(__DIR__);

        $this->registerCore();

        $this->config = new Config();

        $timezone = Helpers::env('APP_TIMEZONE', 'UTC');
        date_default_timezone_set($timezone);

        $this->bootstrapServices();

        $this->plugin = new Plugin();

        $this->loadPlugins();
        $this->plugin->run();

        $this->kernel = new Kernel($this);
    }

    protected function registerCore(): void
    {
        $loader = new Autoloader();
        $loader->register();

        $loader->addNamespace('Caracal\\Core', __DIR__);
        $loader->addNamespace('App\\Modules', $this->basePath . '/app/Modules');
		
		if (!class_exists('CUID')) {
			class_alias(\Caracal\Core\CUID::class, 'CUID');
		}	
    }

    protected function bootstrapServices(): void
    {
        if ($this->config->get('db.enabled', true)) {
            try {
                $this->database = new Database($this->config);
            } catch (\Throwable $e) {
                $this->database = null;
            }
        }
    }

    protected function loadPlugins(): void
    {
        $pluginPath = $this->path('plugins');

        if (!is_dir($pluginPath)) {
            return;
        }

        foreach (glob($pluginPath . '/*.php') as $file) {
            $this->plugin->load($file);
        }
    }

    public function plugins(): Plugin
    {
        return $this->plugin;
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function config(): Config
    {
        return $this->config;
    }

    public function cache(): Cache
    {
        if ($this->cache === null) {
            $this->cache = new Cache();
        }
        return $this->cache;
    }

    public function db(): ?Database
    {
        return $this->database;
    }

    public function path(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function run(): void
    {
        $this->kernel->handle();
    }
}