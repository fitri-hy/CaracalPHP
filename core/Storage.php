<?php
namespace Caracal\Core;

class Storage
{
    protected string $base;
    protected string $driver;
    protected int $maxUploadSize;

    public function __construct(string $path = null)
    {
        $this->driver = strtolower(Helpers::env('FILESYSTEM_DRIVER', 'local'));
        $this->maxUploadSize = $this->parseSize(Helpers::env('UPLOAD_MAX_SIZE', '5M'));

        if ($this->driver === 'local') {
            $this->base = $path ?: __DIR__ . '/../storage/uploads';
            if (!is_dir($this->base)) mkdir($this->base, 0755, true);
        } else {
            throw new \Exception("Storage driver {$this->driver} not supported yet.");
        }
    }

    public function put(string $filename, string $content): bool
    {
        $this->checkFileSize(strlen($content));

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

    protected function checkFileSize(int $sizeBytes): void
    {
        if ($sizeBytes > $this->maxUploadSize) {
            throw new \Exception("File size exceeds the maximum allowed ({$this->maxUploadSize} bytes).");
        }
    }

    protected function parseSize(string $size): int
    {
        $unit = strtoupper(substr($size, -1));
        $num  = (int) $size;
        return match ($unit) {
            'G' => $num * 1024 * 1024 * 1024,
            'M' => $num * 1024 * 1024,
            'K' => $num * 1024,
            default => $num,
        };
    }
}