<?php
namespace Caracal\Core;

class Plugin
{
    protected array $plugins = [];
    protected array $services = [];
    protected array $instances = [];
    protected array $hooks = [];

    public function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception("Plugin file {$path} not found");
        }

        $plugin = include $path;

        if (!is_array($plugin)) {
            throw new \Exception("Plugin file must return an array");
        }

        $this->plugins[] = $plugin;
    }

    public function run(): void
    {
        usort($this->plugins, fn($a, $b) =>
            ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0)
        );

        foreach ($this->plugins as $plugin) {
            if (isset($plugin['register']) && is_callable($plugin['register'])) {
                $plugin['register']($this);
            }
        }

        foreach ($this->plugins as $plugin) {
            if (isset($plugin['boot']) && is_callable($plugin['boot'])) {
                $plugin['boot']($this);
            }

            if (isset($plugin['callback']) && is_callable($plugin['callback'])) {
                $plugin['callback']($this);
            }
        }
    }

    public function set(string $name, $service): void
    {
        $this->services[$name] = $service;
    }

    public function singleton(string $name, callable $factory): void
    {
        $this->services[$name] = $factory;
        $this->instances[$name] = null;
    }

    public function get(string $name)
    {
        if (!isset($this->services[$name])) {
            return null;
        }

        if (array_key_exists($name, $this->instances)) {
            if ($this->instances[$name] === null) {
                $this->instances[$name] = ($this->services[$name])($this);
            }
            return $this->instances[$name];
        }

        if (is_callable($this->services[$name])) {
            return ($this->services[$name])($this);
        }

        return $this->services[$name];
    }

    public function on(string $event, callable $callback): void
    {
        $this->hooks[$event][] = $callback;
    }

    public function trigger(string $event, ...$params): void
    {
        if (!empty($this->hooks[$event])) {
            foreach ($this->hooks[$event] as $callback) {
                $callback(...$params);
            }
        }
    }

    public function all(): array
    {
        return $this->plugins;
    }
}
