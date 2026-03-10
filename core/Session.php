<?php
namespace Caracal\Core;

use Predis\Client;
use Caracal\Core\Database;

class Session
{
    protected string $cipher = 'AES-256-CBC';
    protected string $key;

    protected string $driver;
    protected int $lifetime;

    protected string $cookieName;
    protected string $prefix = 'caracal_session:';

    protected ?Client $redis = null;

    protected bool $started = false;

    public function __construct()
    {
        $this->key = hash('sha256', Helpers::env('APP_KEY'), true);

        $this->driver   = strtolower(Helpers::env('SESSION_DRIVER', 'file'));
        $this->lifetime = (int) Helpers::env('SESSION_LIFETIME', 7200);

        $this->cookieName = Helpers::env('SESSION_COOKIE', 'caracal_session');

        if ($this->driver === 'redis') {
            $this->setupRedis();
        }

        if ($this->driver === 'database') {
            $this->setupDatabase();
        }

        if ($this->driver === 'file') {
            $this->setupFile();
        }
    }

    protected function start(): void
    {
        if ($this->started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {

            session_name($this->cookieName);

            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => isset($_SERVER['HTTPS']),
                'cookie_samesite' => 'Lax',
                'cookie_lifetime' => $this->lifetime,
            ]);
        }

        $this->started = true;

        $this->ageFlash();
    }

    protected function setupFile(): void
    {
        $path = __DIR__ . '/../storage/sessions';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        session_save_path($path);
    }

    protected function setupRedis(): void
    {
        $config = Application::getInstance()->config();

        $this->redis = new Client([
            'scheme'   => 'tcp',
            'host'     => $config->get('REDIS_HOST', '127.0.0.1'),
            'port'     => (int)$config->get('REDIS_PORT', 6379),
            'password' => $config->get('REDIS_PASSWORD', null),
        ]);
    }

    protected function setupDatabase(): void
    {
        $db = Application::getInstance()->db();

        if (!$db || !$db->isConnected()) {
            throw new \RuntimeException("Database session driver requires active DB.");
        }

        $capsule = $db->capsule();

        if (!$capsule::schema()->hasTable('sessions')) {

            $capsule::schema()->create('sessions', function ($table) {

                $table->string('id')->primary();
                $table->text('payload');
                $table->integer('expires');

            });

        }

        session_set_save_handler(
            [$this, 'dbOpen'],
            [$this, 'dbClose'],
            [$this, 'dbRead'],
            [$this, 'dbWrite'],
            [$this, 'dbDestroy'],
            [$this, 'dbGc']
        );
    }

    protected function encrypt(mixed $data): string
    {
        $plaintext = serialize($data);

        $iv = random_bytes(16);

        $ciphertext = openssl_encrypt(
            $plaintext,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $mac = hash_hmac('sha256', $iv.$ciphertext, $this->key, true);

        return base64_encode($iv.$mac.$ciphertext);
    }

    protected function decrypt(string $payload): mixed
    {
        $decoded = base64_decode($payload);

        if (!$decoded || strlen($decoded) < 48) {
            return null;
        }

        $iv  = substr($decoded, 0, 16);
        $mac = substr($decoded, 16, 32);
        $ct  = substr($decoded, 48);

        $calc = hash_hmac('sha256', $iv.$ct, $this->key, true);

        if (!hash_equals($mac, $calc)) {
            return null;
        }

        $plaintext = openssl_decrypt(
            $ct,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $plaintext !== false ? unserialize($plaintext) : null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();

        $_SESSION[$key] = $this->encrypt($value);

        if ($this->driver === 'redis' && $this->redis) {

            $this->redis->setex(
                $this->prefix.$this->id(),
                $this->lifetime,
                serialize($_SESSION)
            );

        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();

        if (!isset($_SESSION[$key])) {
            return $default;
        }

        $val = $this->decrypt($_SESSION[$key]);

        return $val ?? $default;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function remove(string $key): void
    {
        $this->start();

        unset($_SESSION[$key]);
    }

    public function regenerate(): void
    {
        $this->start();

        session_regenerate_id(true);
    }

    public function id(): string
    {
        $this->start();

        return session_id();
    }

    public function flash(string $key, mixed $value = null): mixed
    {
        $this->start();

        if ($value !== null) {

            $_SESSION['_flash']['new'][$key] = $this->encrypt($value);

            return null;
        }

        $val = $_SESSION['_flash']['old'][$key] ?? null;

        unset($_SESSION['_flash']['old'][$key]);

        return $val ? $this->decrypt($val) : null;
    }

    protected function ageFlash(): void
    {
        $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'] ?? [];

        $_SESSION['_flash']['new'] = [];
    }

    public function all(): array
    {
        $this->start();

        $data = [];

        foreach ($_SESSION as $k => $v) {

            if ($k === '_flash') {
                continue;
            }

            $data[$k] = $this->decrypt($v) ?? $v;

        }

        return $data;
    }

    public function clear(): void
    {
        $this->start();

        $_SESSION = [];

        session_destroy();
    }

    protected function dbOpen($path, $name) { return true; }

    protected function dbClose() { return true; }

    protected function dbRead($id)
    {
        $row = Database::connection()
            ->table('sessions')
            ->where('id', $id)
            ->first();

        if (!$row) {
            return '';
        }

        if ($row->expires < time()) {
            return '';
        }

        return $row->payload;
    }

    protected function dbWrite($id, $data)
    {
        $db = Database::connection()->table('sessions');

        $expires = time() + $this->lifetime;

        if ($db->where('id', $id)->exists()) {

            $db->where('id', $id)->update([
                'payload' => $data,
                'expires' => $expires
            ]);

        } else {

            $db->insert([
                'id' => $id,
                'payload' => $data,
                'expires' => $expires
            ]);

        }

        return true;
    }

    protected function dbDestroy($id)
    {
        Database::connection()
            ->table('sessions')
            ->where('id', $id)
            ->delete();

        return true;
    }

    protected function dbGc($maxlifetime)
    {
        Database::connection()
            ->table('sessions')
            ->where('expires', '<', time())
            ->delete();

        return true;
    }
}