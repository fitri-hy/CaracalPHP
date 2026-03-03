<?php
namespace Caracal\Core;

class Autoloader
{
    protected array $prefixes = [];

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    public function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->prefixes[$prefix][] = $baseDir;
    }

    public function loadClass(string $class): void
    {
        foreach ($this->prefixes as $prefix => $dirs) {
            if (str_starts_with($class, $prefix)) {
                $relative = substr($class, strlen($prefix));
                $file = str_replace('\\', '/', $relative) . '.php';
                foreach ($dirs as $dir) {
                    $path = $dir . $file;
                    if (file_exists($path)) {
                        require $path;
                        return;
                    }
                }
            }
        }
    }
}