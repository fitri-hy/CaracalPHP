<?php

namespace Caracal\Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;
use PDOException;

class Database
{
    protected Capsule $capsule;
    protected bool $connected = false;

    public function __construct(Config $config)
    {
        $db = $config->get('db', []);

        if (empty($db['enabled'])) {
            return;
        }

        $this->capsule = new Capsule();

        try {
            $this->capsule->addConnection(
                $this->buildConnectionConfig($db)
            );

            $this->capsule->setAsGlobal();
            $this->capsule->bootEloquent();

            $this->connected = true;

        } catch (PDOException $e) {
            throw new RuntimeException(
                "Database connection failed: ".$e->getMessage()
            );
        }
    }

    protected function buildConnectionConfig(array $db): array
    {
        $driver = $db['driver'] ?? 'mysql';

        return match ($driver) {

            'sqlite' => [
                'driver'   => 'sqlite',
                'database' => $db['name'] ?: $this->sqlitePath(),
                'prefix'   => '',
            ],

            default => [
                'driver'    => 'mysql',
                'host'      => $db['host'] ?? '127.0.0.1',
                'port'      => $db['port'] ?? 3306,
                'database'  => $db['name'] ?? '',
                'username'  => $db['user'] ?? '',
                'password'  => $db['pass'] ?? '',
                'charset'   => $db['charset'] ?? 'utf8mb4',
                'collation' => $db['collation'] ?? 'utf8mb4_unicode_ci',
                'prefix'    => $db['prefix'] ?? '',
                'strict'    => true,
            ],
        };
    }

    protected function sqlitePath(): string
    {
        $path = dirname(__DIR__).'/../database/database.sqlite';

        if (!file_exists($path)) {
            touch($path);
        }

        return $path;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function capsule(): Capsule
    {
        if (!$this->connected) {
            throw new RuntimeException("Database is not connected.");
        }

        return $this->capsule;
    }

    public static function connection(): Capsule
    {
        $app = Application::getInstance();

        if (!$app->db() || !$app->db()->isConnected()) {
            throw new RuntimeException(
                "Database disabled. Set DB_ENABLED=true"
            );
        }

        return $app->db()->capsule();
    }
}