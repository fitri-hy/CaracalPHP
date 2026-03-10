<?php

namespace Caracal\Core;

class Application
{
    protected static ?self $instance = null;

    public Config $config;
    public Kernel $kernel;
    public ?Database $database = null;

    protected ?Cache $cache = null;
    protected Plugin $plugin;

    protected string $basePath;

    protected array $bindings = [];
    protected array $instances = [];

    protected bool $booted = false;

    private function __construct()
    {
        $this->basePath = dirname(__DIR__);

        $this->registerCore();

        $this->config = new Config();

        $timezone = Helpers::env('APP_TIMEZONE', 'UTC');
        date_default_timezone_set($timezone);

        $this->registerBaseServices();

        $this->bootstrapServices();

        $this->plugin = new Plugin();

        $this->loadPlugins();

        $this->boot();

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

    public function bind(string $id, callable $resolver): void
    {
        $this->bindings[$id] = $resolver;
    }

    public function singleton(string $id, callable $resolver): void
    {
        $this->bindings[$id] = function ($app) use ($resolver, $id) {

            if (!isset($this->instances[$id])) {
                $this->instances[$id] = $resolver($app);
            }

            return $this->instances[$id];
        };
    }

    public function make(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->bindings[$id])) {
            throw new \RuntimeException("Service {$id} not found.");
        }

        return $this->bindings[$id]($this);
    }

    protected function registerBaseServices(): void
    {
        $this->singleton('cache', function () {
            return new Cache();
        });

        $this->singleton('config', function () {
            return $this->config;
        });

        $this->singleton('db', function () {

            if (!$this->config->get('db.enabled', true)) {
                return null;
            }

            try {
                return new Database($this->config);
            } catch (\Throwable) {
                return null;
            }
        });

        $this->singleton('plugins', function () {
            return $this->plugin;
        });
    }

    protected function bootstrapServices(): void
    {
        if ($this->config->get('db.enabled', true)) {

            try {
                $this->database = $this->make('db');
            } catch (\Throwable) {
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

    protected function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->plugin->run();

        $this->booted = true;
    }

    public function config(): Config
    {
        return $this->config;
    }

    public function cache(): Cache
    {
        return $this->make('cache');
    }

    public function db(): ?Database
    {
        return $this->database;
    }

    public function plugins(): Plugin
    {
        return $this->plugin;
    }

    public function path(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function run(): void
    {
        $this->kernel->handle();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }
}