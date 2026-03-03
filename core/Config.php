<?php
namespace Caracal\Core;

use Dotenv\Dotenv;

class Config
{
    protected array $items = [];
    protected static bool $envLoaded = false;

    public function __construct()
    {
        $this->loadEnv();
        $this->items = $this->loadConfigs();
    }

    protected function loadEnv(): void
    {
        if (self::$envLoaded) return;

        $root = dirname(__DIR__);
        $env = $root.'/.env';

        if (is_file($env)) {
            Dotenv::createImmutable($root)->safeLoad();
        }

        self::$envLoaded = true;
    }

    protected function loadConfigs(): array
    {
        $path = dirname(__DIR__).'/config/config.php';

        if (!is_file($path)) return [];

        $config = include $path;

        return is_array($config) ? $config : [];
    }

    public function get(string $key, mixed $default=null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment,$value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }

    public function all(): array
    {
        return $this->items;
    }
}