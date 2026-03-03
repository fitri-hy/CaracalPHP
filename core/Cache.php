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

    public function __construct()
    {
        $config = Application::getInstance()->config();

        $this->enabled = filter_var($config->get('cache.enabled', true), FILTER_VALIDATE_BOOLEAN);
        $this->driver  = strtolower($config->get('cache.driver', 'file')); // file atau redis
        $this->defaultTTL = (int) $config->get('cache.ttl', 3600);
        $this->cachePath = dirname(__DIR__) . '/storage/cache/';

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }

        if (!$this->enabled) {
            $this->redis = null;
            return;
        }

        if ($this->driver === 'redis' && class_exists(Client::class) && $config->get('cache.redis.host')) {
            $redisConfig = [
                'scheme' => 'tcp',
                'host' => $config->get('cache.redis.host', '127.0.0.1'),
                'port' => (int) $config->get('cache.redis.port', 6379),
            ];

            $redisPassword = $config->get('cache.redis.password', null);
            if (!empty($redisPassword)) {
                $redisConfig['password'] = $redisPassword;
            }

            try {
                $this->redis = new Client($redisConfig);
                $this->redis->ping();
            } catch (\Exception $e) {
                $this->redis = null;
            }
        }
    }

    public function set(string $key, mixed $value, int $ttl = null): void
    {
        if (!$this->enabled) return;

        $ttl = $ttl ?? $this->defaultTTL;

        if ($this->driver === 'redis' && $this->redis) {
            try {
                $this->redis->setex($key, $ttl, serialize($value));
            } catch (\Exception $e) {
                $this->redis = null;
            }
        }

        $file = $this->getFilePath($key);
        file_put_contents($file, serialize([
            'value' => $value,
            'expires' => time() + $ttl
        ]));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) return $default;

        if ($this->driver === 'redis' && $this->redis) {
            try {
                $val = $this->redis->get($key);
                if ($val !== null) return unserialize($val);
            } catch (\Exception $e) {
                $this->redis = null;
            }
        }

        $file = $this->getFilePath($key);
        if (!file_exists($file)) return $default;

        $data = unserialize(file_get_contents($file));
        if (isset($data['expires']) && time() > $data['expires']) {
            @unlink($file);
            return $default;
        }

        return $data['value'] ?? $default;
    }

    public function delete(string $key): void
    {
        if (!$this->enabled) return;

        if ($this->driver === 'redis' && $this->redis) {
            try {
                $this->redis->del([$key]);
            } catch (\Exception $e) {
                $this->redis = null;
            }
        }

        $file = $this->getFilePath($key);
        @unlink($file);
    }

    protected function getFilePath(string $key): string
    {
        $safeKey = str_replace(['/', '\\'], '_', $key);
        return $this->cachePath . $safeKey . '.cache';
    }

    public function getDefaultTTL(): int
    {
        return $this->defaultTTL;
    }
	
	public function clearAll(): void
	{
		if (!$this->enabled) return;

		if ($this->driver === 'redis' && $this->redis) {
			$this->redis->flushdb();
		}

		$files = glob($this->cachePath . '*.cache');
		foreach ($files as $file) {
			@unlink($file);
		}
	}
}