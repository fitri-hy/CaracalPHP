<?php
namespace Caracal\Core;

class Module
{
    protected array $modules = [];

    public function register(string $name, string $path): void
    {
        if (!is_dir($path)) throw new \Exception("Module path {$path} not found");
        $this->modules[$name] = $path;
    }

    public function all(): array
    {
        return $this->modules;
    }
}