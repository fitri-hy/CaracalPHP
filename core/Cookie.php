<?php
namespace Caracal\Core;

class Cookie
{
    protected static string $defaultPath = '/';
    protected static string $defaultSameSite = 'Lax';

    public static function set(
        string $name,
        mixed $value,
        int $minutes = 60,
        string $path = '/',
        string $domain = '',
        bool $secure = null,
        bool $httponly = true,
        string $samesite = 'Lax'
    ): void {

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $secure = $secure ?? self::isHttps();

        setcookie($name, $value, [
            'expires'  => time() + ($minutes * 60),
            'path'     => $path ?: self::$defaultPath,
            'domain'   => $domain ?: self::domain(),
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite ?: self::$defaultSameSite,
        ]);

        $_COOKIE[$name] = $value;
    }

    public static function forever(string $name, mixed $value): void
    {
        self::set($name, $value, 60 * 24 * 365 * 5);
    }

    public static function get(string $name, mixed $default = null): mixed
    {
        if (!isset($_COOKIE[$name])) {
            return $default;
        }

        $value = $_COOKIE[$name];

        $json = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        $unserialized = @unserialize($value);

        if ($unserialized !== false || $value === 'b:0;') {
            return $unserialized;
        }

        return $value;
    }

    public static function pull(string $name, mixed $default = null): mixed
    {
        $value = self::get($name, $default);

        self::delete($name);

        return $value;
    }

    public static function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public static function delete(string $name, string $path = '/', string $domain = ''): void
    {
        setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => $path ?: self::$defaultPath,
            'domain'   => $domain ?: self::domain(),
            'secure'   => self::isHttps(),
            'httponly' => true,
            'samesite' => self::$defaultSameSite,
        ]);

        unset($_COOKIE[$name]);
    }

    public static function clearAll(): void
    {
        foreach ($_COOKIE as $name => $value) {
            self::delete($name);
        }
    }

    public static function all(): array
    {
        return $_COOKIE;
    }

    protected static function domain(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    protected static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? null) == 443;
    }
}