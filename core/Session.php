<?php
namespace Caracal\Core;

use Predis\Client;
use Caracal\Core\Database;

class Session
{
    protected string $key;
    protected string $cipher = 'AES-256-CBC';
    protected string $driver;
    protected int $lifetime;
    protected ?Client $redis = null;

    public function __construct()
    {
        $this->key = hash('sha256', Helpers::env('APP_KEY'), true);
        
        $this->driver = strtolower(Helpers::env('SESSION_DRIVER', 'file'));
        $this->lifetime = (int) Helpers::env('SESSION_LIFETIME', 7200);

        if ($this->driver === 'redis') {
            $this->setupRedis();
        } elseif ($this->driver === 'database') {
            $this->setupDatabase();
        } else {
            $this->setupFile();
        }
    }

    protected function setupFile(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $path = __DIR__ . '/../storage/sessions';
            if (!is_dir($path)) mkdir($path, 0755, true);

            session_save_path($path);
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
                'cookie_lifetime' => $this->lifetime,
            ]);
        }
    }

    protected function setupRedis(): void
    {
        $config = Application::getInstance()->config();
        try {
            $this->redis = new Client([
                'scheme' => 'tcp',
                'host' => $config->get('REDIS_HOST', '127.0.0.1'),
                'port' => (int)$config->get('REDIS_PORT', 6379),
                'password' => $config->get('REDIS_PASSWORD', null),
            ]);
            $this->redis->ping();
        } catch (\Exception $e) {
            $this->redis = null;
            throw new \RuntimeException("Redis session driver failed: " . $e->getMessage());
        }
    }

    protected function setupDatabase(): void
    {
        $db = Application::getInstance()->db();
        if (!$db || !$db->isConnected()) {
            throw new \RuntimeException("Database session driver requires an active DB connection.");
        }

        $capsule = $db->capsule();
        if (!$capsule::schema()->hasTable('sessions')) {
            $capsule::schema()->create('sessions', function ($table) {
                $table->string('id')->primary();
                $table->text('payload');
                $table->integer('expires');
            });
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_set_save_handler(
                [$this, 'dbOpen'],
                [$this, 'dbClose'],
                [$this, 'dbRead'],
                [$this, 'dbWrite'],
                [$this, 'dbDestroy'],
                [$this, 'dbGc']
            );
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
                'cookie_lifetime' => $this->lifetime,
            ]);
        }
    }

    protected function encrypt(mixed $data): string
    {
        $plaintext = serialize($data);
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        $mac = hash_hmac('sha256', $iv.$ciphertext, $this->key, true);
        return base64_encode($iv . $mac . $ciphertext);
    }

    protected function decrypt(string $payload): mixed
    {
        $decoded = base64_decode($payload);
        if (strlen($decoded) < 48) return null;

        $iv = substr($decoded, 0, 16);
        $mac = substr($decoded, 16, 32);
        $ciphertext = substr($decoded, 48);

        $calc_mac = hash_hmac('sha256', $iv.$ciphertext, $this->key, true);
        if (!hash_equals($mac, $calc_mac)) return null;

        $plaintext = openssl_decrypt($ciphertext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        return $plaintext !== false ? unserialize($plaintext) : null;
    }

    public function set(string $key, mixed $value): void
    {
        if ($this->driver === 'file' || $this->driver === 'database') {
            $_SESSION[$key] = $this->encrypt($value);
        } elseif ($this->driver === 'redis' && $this->redis) {
            $this->redis->setex($this->sessionKey($key), $this->lifetime, $this->encrypt($value));
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->driver === 'file' || $this->driver === 'database') {
            if (!isset($_SESSION[$key])) return $default;
            $val = $this->decrypt($_SESSION[$key]);
            return $val ?? $default;
        } elseif ($this->driver === 'redis' && $this->redis) {
            $val = $this->redis->get($this->sessionKey($key));
            return $val ? ($this->decrypt($val) ?? $default) : $default;
        }
        return $default;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function remove(string $key): void
    {
        if ($this->driver === 'file' || $this->driver === 'database') {
            unset($_SESSION[$key]);
        } elseif ($this->driver === 'redis' && $this->redis) {
            $this->redis->del([$this->sessionKey($key)]);
        }
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function id(): string
    {
        if (!session_id()) $this->regenerate();
        return session_id();
    }

    public function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $this->encrypt($value);
            return null;
        }

        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val !== null ? $this->decrypt($val) : null;
    }

    public function all(): array
    {
        $data = [];
        foreach ($_SESSION as $k => $v) {
            if (!str_starts_with($k, '_flash')) {
                $data[$k] = $this->decrypt($v) ?? $v;
            }
        }
        return $data;
    }

    public function clear(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        if ($this->driver === 'redis' && $this->redis) {
            $this->redis->flushdb();
        }
    }

    protected function sessionKey(string $key): string
    {
        return 'session:' . $this->id() . ':' . $key;
    }

    public function dbOpen($savePath, $sessionName) { return true; }
    public function dbClose() { return true; }
    public function dbRead($id) {
        $row = Database::connection()->table('sessions')->where('id', $id)->first();
        return ($row && $row->expires >= time()) ? $row->payload : '';
    }
    public function dbWrite($id, $data) {
        $db = Database::connection()->table('sessions');
        $expires = time() + $this->lifetime;
        if ($db->where('id', $id)->exists()) {
            $db->where('id', $id)->update(['payload'=>$data, 'expires'=>$expires]);
        } else {
            $db->insert(['id'=>$id, 'payload'=>$data, 'expires'=>$expires]);
        }
        return true;
    }
    public function dbDestroy($id) {
        Database::connection()->table('sessions')->where('id', $id)->delete();
        return true;
    }
    public function dbGc($maxlifetime) {
        Database::connection()->table('sessions')->where('expires', '<', time())->delete();
        return true;
    }
}