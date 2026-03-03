<?php
namespace Caracal\Core;

class Cookie
{
    public static function set(
        string $name,
        mixed $value,
        int $minutes = 60,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = true
    ): void {
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        setcookie($name, $value, [
            'expires'  => time() + ($minutes * 60),
            'path'     => $path,
            'domain'   => $domain ?: $_SERVER['HTTP_HOST'] ?? '',
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => 'Lax', // default aman
        ]);

        $_COOKIE[$name] = $value;
    }

    public static function get(string $name, mixed $default = null): mixed
    {
        if (!isset($_COOKIE[$name])) {
            return $default;
        }

        $value = $_COOKIE[$name];

        $unserialized = @unserialize($value);
        return $unserialized !== false || $value === 'b:0;' ? $unserialized : $value;
    }

    public static function delete(string $name, string $path = '/', string $domain = ''): void
    {
        setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => $path,
            'domain'   => $domain ?: $_SERVER['HTTP_HOST'] ?? '',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        unset($_COOKIE[$name]);
    }

    public static function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public static function clearAll(): void
    {
        foreach ($_COOKIE as $name => $value) {
            self::delete($name);
        }
    }
}