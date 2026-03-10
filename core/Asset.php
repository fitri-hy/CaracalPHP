<?php
namespace Caracal\Core;

class Asset
{
    protected string $basePath;
    protected string $assetPath = '/assets';

    public function __construct()
    {
        $this->basePath = dirname(__DIR__) . '/public/assets';
    }

    public function url(string $file): string
    {
        $base = $this->baseUrl();
        $file = ltrim($file, '/');

        return $base . $this->assetPath . '/' . $file;
    }

    protected function baseUrl(): string
    {
        $appUrl = rtrim(Helpers::env('APP_URL', 'http://localhost'), '/');

        $parts = parse_url($appUrl);

        $scheme = $parts['scheme'] ?? 'http';
        $host   = $parts['host'] ?? 'localhost';
        $port   = $_SERVER['SERVER_PORT'] ?? null;

        if ($port && !str_contains($host, ":$port")) {
            if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
                $host .= ':' . $port;
            }
        }

        return $scheme . '://' . $host;
    }

    public function path(string $file): string
    {
        return $this->basePath . '/' . ltrim($file, '/');
    }

    public function exists(string $file): bool
    {
        return file_exists($this->path($file));
    }

    public function version(string $file): string
    {
        $path = $this->path($file);

        if (!file_exists($path)) {
            return $this->url($file);
        }

        $version = filemtime($path);

        return $this->url($file) . '?v=' . $version;
    }

    public function css(string $file): string
    {
        return '<link rel="stylesheet" href="' . $this->version($file) . '">';
    }

    public function js(string $file): string
    {
        return '<script src="' . $this->version($file) . '"></script>';
    }

    public function image(string $file, string $alt = ''): string
    {
        return '<img src="' . $this->version($file) . '" alt="' . htmlspecialchars($alt) . '">';
    }
}