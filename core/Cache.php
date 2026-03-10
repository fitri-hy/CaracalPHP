<?php
namespace Caracal\Core;

use Predis\Client;

class Cache
{
    protected ?Client $redis = null;
    protected bool $enabled = true;
    protected int $defaultTTL = 3600;
    protected string $cachePath;
    protected string $driver;
    protected string $prefix = 'caracal:';

    public function __construct()
    {
        $config = Application::getInstance()->config();

        $this->enabled    = filter_var($config->get('cache.enabled', true), FILTER_VALIDATE_BOOLEAN);
        $this->driver     = strtolower($config->get('cache.driver', 'file'));
        $this->defaultTTL = (int) $config->get('cache.ttl', 3600);
        $this->prefix     = (string) $config->get('cache.prefix', 'caracal:');
        $this->cachePath  = dirname(__DIR__) . '/storage/cache/';

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }

        if (!$this->enabled) {
            return;
        }

        if ($this->driver === 'redis' && class_exists(Client::class) && $config->get('cache.redis.host')) {

            try {

                $this->redis = new Client([
                    'scheme' => 'tcp',
                    'host'   => $config->get('cache.redis.host', '127.0.0.1'),
                    'port'   => (int)$config->get('cache.redis.port', 6379),
                    'password' => $config->get('cache.redis.password', null),
                ]);

                $this->redis->ping();

            } catch (\Throwable) {
                $this->redis = null;
            }
        }
    }

    public function set(string $key, mixed $value, int $ttl = null): void
    {
        if (!$this->enabled) return;

        $ttl = $ttl ?? $this->defaultTTL;
        $key = $this->prefix . $key;

        if ($this->redis) {
            try {
                $this->redis->setex($key, $ttl, serialize($value));
                return;
            } catch (\Throwable) {
                $this->redis = null;
            }
        }

        $file = $this->getFilePath($key);

        $data = serialize([
            'value'   => $value,
            'expires' => time() + $ttl
        ]);

        $tmp = $file . '.tmp';
        file_put_contents($tmp, $data, LOCK_EX);
        rename($tmp, $file);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) return $default;

        $key = $this->prefix . $key;

        if ($this->redis) {
            try {

                $val = $this->redis->get($key);

                if ($val !== null) {
                    return unserialize($val);
                }

            } catch (\Throwable) {
                $this->redis = null;
            }
        }

        $file = $this->getFilePath($key);

        if (!is_file($file)) {
            return $default;
        }

        $raw = file_get_contents($file);

        $data = @unserialize($raw);

        if (!is_array($data)) {
            @unlink($file);
            return $default;
        }

        if (isset($data['expires']) && time() > $data['expires']) {
            @unlink($file);
            return $default;
        }

        return $data['value'] ?? $default;
    }

    public function has(string $key): bool
    {
        return $this->get($key, '__null__') !== '__null__';
    }

    public function delete(string $key): void
    {
        if (!$this->enabled) return;

        $key = $this->prefix . $key;

        if ($this->redis) {
            try {
                $this->redis->del([$key]);
            } catch (\Throwable) {
                $this->redis = null;
            }
        }

        $file = $this->getFilePath($key);

        if (is_file($file)) {
            @unlink($file);
        }
    }

    public function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();

        $this->set($key, $value, $ttl);

        return $value;
    }

    protected function getFilePath(string $key): string
    {
        $hash = md5($key);
        return $this->cachePath . $hash . '.cache';
    }

    public function clearAll(): void
    {
        if (!$this->enabled) return;

        if ($this->redis) {
            try {
                $this->redis->flushdb();
            } catch (\Throwable) {}
        }

        foreach (glob($this->cachePath . '*.cache') as $file) {
            @unlink($file);
        }
    }

    public function getDefaultTTL(): int
    {
        return $this->defaultTTL;
    }
}