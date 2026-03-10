<?php
namespace Caracal\Core;

class Autoloader
{
    protected array $prefixes = [];
    protected array $classMap = [];
    protected array $fallbackDirs = [];

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass'], true, true);
    }

    public function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }

        $this->prefixes[$prefix][] = $baseDir;
    }

    public function addFallbackDir(string $dir): void
    {
        $this->fallbackDirs[] = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function addClassMap(array $map): void
    {
        $this->classMap = array_merge($this->classMap, $map);
    }

    public function loadClass(string $class): bool
    {
        if (isset($this->classMap[$class])) {
            require $this->classMap[$class];
            return true;
        }

        foreach ($this->prefixes as $prefix => $dirs) {

            if (!str_starts_with($class, $prefix)) {
                continue;
            }

            $relative = substr($class, strlen($prefix));
            $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            foreach ($dirs as $dir) {

                $file = $dir . $relativePath;

                if (is_file($file)) {
                    require $file;
                    return true;
                }
            }
        }

        foreach ($this->fallbackDirs as $dir) {

            $file = $dir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            if (is_file($file)) {
                require $file;
                return true;
            }
        }

        return false;
    }
}