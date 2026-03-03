<?php
namespace Caracal\Core;

class Storage
{
    protected string $base;

    public function __construct(string $path = null)
    {
        $this->base = $path ?: __DIR__ . '/../storage/uploads';
        if (!is_dir($this->base)) mkdir($this->base, 0755, true);
    }

    public function put(string $filename, string $content): bool
    {
        $fullPath = $this->base.'/'.$filename;
        $dir = dirname($fullPath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return file_put_contents($fullPath, $content) !== false;
    }

    public function get(string $filename): ?string
    {
        $file = $this->base.'/'.$filename;
        return file_exists($file) ? file_get_contents($file) : null;
    }

    public function delete(string $filename): bool
    {
        $file = $this->base.'/'.$filename;
        return file_exists($file) ? unlink($file) : false;
    }

    public function path(string $filename): string
    {
        return $this->base.'/'.$filename;
    }

    public function exists(string $filename): bool
    {
        return file_exists($this->base.'/'.$filename);
    }

    public function makeDir(string $dir): bool
    {
        $path = $this->base.'/'.$dir;
        return is_dir($path) || mkdir($path, 0755, true);
    }
}