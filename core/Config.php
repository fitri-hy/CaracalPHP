<?php

namespace Caracal\Core;

use Dotenv\Dotenv;

class Config
{
    protected array $items = [];

    protected static bool $envLoaded = false;

    protected string $configPath;

    protected string $cacheFile;

    public function __construct()
    {
        $root = dirname(__DIR__);

        $this->configPath = $root . '/config/';
        $this->cacheFile  = $root . '/storage/cache/config.php';

        $this->loadEnv();

        if ($this->loadFromCache()) {
            return;
        }

        $this->items = $this->loadConfigFiles();

        $this->loadEnvConfigs();
    }

    protected function loadEnv(): void
    {
        if (self::$envLoaded) {
            return;
        }

        $root = dirname(__DIR__);
        $env  = $root . '/.env';

        if (is_file($env)) {
            Dotenv::createImmutable($root)->safeLoad();
        }

        self::$envLoaded = true;
    }

    protected function loadConfigFiles(): array
    {
        if (!is_dir($this->configPath)) {
            return [];
        }

        $configs = [];

        foreach (glob($this->configPath . '*.php') as $file) {

            $key = basename($file, '.php');

            $data = include $file;

            if (is_array($data)) {
                $configs[$key] = $data;
            }
        }

        return $configs;
    }

    protected function loadEnvConfigs(): void
    {
        $this->items['app'] = [
            'name'     => $this->env('APP_NAME', 'Caracal'),
            'env'      => $this->env('APP_ENV', 'production'),
            'debug'    => filter_var($this->env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
            'url'      => $this->env('APP_URL', 'http://localhost'),
            'key'      => $this->env('APP_KEY', ''),
            'timezone' => $this->env('APP_TIMEZONE', 'UTC'),
            'csrf'     => filter_var($this->env('CSRF_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        ];

        $this->items['db'] = [
            'enabled'   => filter_var($this->env('DB_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'driver'    => $this->env('DB_DRIVER', 'mysql'),
            'host'      => $this->env('DB_HOST', '127.0.0.1'),
            'port'      => (int) $this->env('DB_PORT', 3306),
            'name'      => $this->env('DB_NAME', ''),
            'user'      => $this->env('DB_USER', ''),
            'pass'      => $this->env('DB_PASS', ''),
            'prefix'    => '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];

        $this->items['cache'] = [
            'enabled' => filter_var($this->env('CACHE_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'driver'  => $this->env('CACHE_DRIVER', 'file'),
            'ttl'     => (int) $this->env('CACHE_TTL', 3600),
            'prefix'  => $this->env('CACHE_PREFIX', 'caracal:'),
            'redis'   => [
                'host'     => $this->env('REDIS_HOST', '127.0.0.1'),
                'port'     => (int) $this->env('REDIS_PORT', 6379),
                'password' => $this->env('REDIS_PASSWORD'),
            ],
        ];

        $this->items['session'] = [
            'driver'   => $this->env('SESSION_DRIVER', 'file'),
            'lifetime' => (int) $this->env('SESSION_LIFETIME', 7200),
            'cookie'   => $this->env('SESSION_COOKIE', 'caracal_session'),
        ];

        $this->items['mail'] = [
            'host'         => $this->env('MAIL_HOST', 'localhost'),
            'port'         => (int) $this->env('MAIL_PORT', 25),
            'user'         => $this->env('MAIL_USER'),
            'pass'         => $this->env('MAIL_PASS'),
            'encryption'   => $this->env('MAIL_ENCRYPTION', 'tls'),
            'from_address' => $this->env('MAIL_FROM_ADDRESS', 'noreply@caracal.local'),
            'from_name'    => $this->env('MAIL_FROM_NAME', 'Caracal'),
        ];

        $this->items['upload'] = [
            'max_size' => $this->env('UPLOAD_MAX_SIZE', '5M'),
            'filesystem_driver' => $this->env('FILESYSTEM_DRIVER', 'local'),
        ];

        $this->items['ws'] = [
            'host'          => $this->env('WS_HOST', '0.0.0.0'),
            'port'          => (int) $this->env('WS_PORT', 8080),
            'logging'       => filter_var($this->env('WS_LOGGING', false), FILTER_VALIDATE_BOOLEAN),
            'use_ssl'       => filter_var($this->env('WS_USE_SSL', false), FILTER_VALIDATE_BOOLEAN),
            'cert_path'     => $this->env('WS_CERT_PATH'),
            'key_path'      => $this->env('WS_KEY_PATH'),
            'auth_enabled'  => filter_var($this->env('WS_AUTH_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'auth_secret'   => $this->env('WS_AUTH_SECRET', ''),
            'ping_interval' => (int) $this->env('WS_PING_INTERVAL', 30),
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);

        $value = $this->items;

        foreach ($segments as $segment) {

            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);

        $array =& $this->items;

        foreach ($segments as $segment) {

            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }

            $array =& $array[$segment];
        }

        $array = $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key, '__missing__') !== '__missing__';
    }

    public function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    protected function loadFromCache(): bool
    {
        if (!is_file($this->cacheFile)) {
            return false;
        }

        $data = include $this->cacheFile;

        if (is_array($data)) {
            $this->items = $data;
            return true;
        }

        return false;
    }

    public function cache(): void
    {
        $dir = dirname($this->cacheFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            $this->cacheFile,
            '<?php return ' . var_export($this->items, true) . ';'
        );
    }

    public function clearCache(): void
    {
        if (is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    public function all(): array
    {
        return $this->items;
    }
}