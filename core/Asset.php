<?php
namespace Caracal\Core;

class Asset
{
    protected string $base;

    public function __construct()
    {
        $this->base = __DIR__ . '/../public/assets';
    }

    public function url(string $file): string
    {
        $appUrl = rtrim(Helpers::env('APP_URL', 'http://localhost'), '/');

        $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'http';
        $host   = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';

        $port = $_SERVER['SERVER_PORT'] ?? null;

        if ($port && !str_contains($host, ":$port")) {
            if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
                $host .= ":$port";
            }
        }

        $base = $scheme . '://' . $host;

        return $base . '/assets/' . ltrim($file, '/');
    }
}