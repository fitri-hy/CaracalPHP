<?php
namespace Caracal\Core;

class Helpers
{
    public static function dd(mixed $var): void
    {
        echo '<pre>'; var_dump($var); echo '</pre>'; 
        exit;
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }

    public static function url(string $path = ''): string
    {
        $appUrl = rtrim(self::env('APP_URL', 'http://localhost'), '/');

        $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'http';
        $host   = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';

        $port = $_SERVER['SERVER_PORT'] ?? null;

        if ($port && !str_contains($host, ":$port")) {
            if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
                $host .= ":$port";
            }
        }

        $base = $scheme . '://' . $host;

        return $base . '/' . ltrim($path, '/');
    }
}