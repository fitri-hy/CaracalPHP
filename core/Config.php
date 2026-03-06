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
        $this->loadEnvConfigs();
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

    protected function loadEnvConfigs(): void
    {
        $this->items['app'] = [
            'name'       => $_ENV['APP_NAME'] ?? 'Caracal',
            'env'        => $_ENV['APP_ENV'] ?? 'production',
            'debug'      => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'url'        => $_ENV['APP_URL'] ?? 'http://localhost',
            'key'        => $_ENV['APP_KEY'] ?? '',
            'timezone'   => $_ENV['APP_TIMEZONE'] ?? 'UTC',
            'csrf'       => filter_var($_ENV['CSRF_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ];

        $this->items['db'] = [
            'enabled'    => filter_var($_ENV['DB_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'driver'     => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host'       => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'       => $_ENV['DB_PORT'] ?? 3306,
            'name'       => $_ENV['DB_NAME'] ?? '',
            'user'       => $_ENV['DB_USER'] ?? '',
            'pass'       => $_ENV['DB_PASS'] ?? '',
            'prefix'     => '',
            'charset'    => 'utf8mb4',
            'collation'  => 'utf8mb4_unicode_ci',
        ];

        $this->items['cache'] = [
            'enabled' => filter_var($_ENV['CACHE_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'driver'  => $_ENV['CACHE_DRIVER'] ?? 'file',
            'ttl'     => (int) ($_ENV['CACHE_TTL'] ?? 3600),
            'redis'   => [
                'host'     => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'port'     => $_ENV['REDIS_PORT'] ?? 6379,
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
            ]
        ];

        $this->items['session'] = [
            'driver'   => $_ENV['SESSION_DRIVER'] ?? 'file',
            'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
        ];

        $this->items['mail'] = [
            'host'       => $_ENV['MAIL_HOST'] ?? 'localhost',
            'port'       => (int) ($_ENV['MAIL_PORT'] ?? 25),
            'user'       => $_ENV['MAIL_USER'] ?? '',
            'pass'       => $_ENV['MAIL_PASS'] ?? '',
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@caracal.local',
            'from_name'    => $_ENV['MAIL_FROM_NAME'] ?? 'Caracal',
        ];

        $this->items['upload'] = [
            'max_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? '5M',
            'filesystem_driver' => $_ENV['FILESYSTEM_DRIVER'] ?? 'local',
        ];

        $this->items['ws'] = [
            'host'          => $_ENV['WS_HOST'] ?? '0.0.0.0',
            'port'          => (int) ($_ENV['WS_PORT'] ?? 8080),
            'logging'       => filter_var($_ENV['WS_LOGGING'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'use_ssl'       => filter_var($_ENV['WS_USE_SSL'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'cert_path'     => $_ENV['WS_CERT_PATH'] ?? null,
            'key_path'      => $_ENV['WS_KEY_PATH'] ?? null,
            'auth_enabled'  => filter_var($_ENV['WS_AUTH_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'auth_secret'   => $_ENV['WS_AUTH_SECRET'] ?? '',
            'ping_interval' => (int) ($_ENV['WS_PING_INTERVAL'] ?? 30),
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
        $this->items[$key] = $value;
    }

    public function all(): array
    {
        return $this->items;
    }
}