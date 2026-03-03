<?php
return [

    'app_name' => $_ENV['APP_NAME'] ?? 'Caracal',
    'app_env' => $_ENV['APP_ENV'] ?? 'development',
    'app_debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'app_url' => $_ENV['APP_URL'] ?? 'http://localhost',

    'db' => [
        'enabled' => filter_var($_ENV['DB_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'driver'  => $_ENV['DB_DRIVER'] ?? 'mysql',
        'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port'    => $_ENV['DB_PORT'] ?? 3306,
        'name'    => $_ENV['DB_NAME'] ?? '',
        'user'    => $_ENV['DB_USER'] ?? '',
        'pass'    => $_ENV['DB_PASS'] ?? '',
        'prefix'  => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.mailtrap.io',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'user' => $_ENV['MAIL_USER'] ?? '',
        'pass' => $_ENV['MAIL_PASS'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@caracal.local',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Caracal',
    ],

    'cache' => [
        'enabled' => filter_var($_ENV['CACHE_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'ttl' => (int) ($_ENV['CACHE_TTL'] ?? 3600),
        'redis' => [
            'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'port' => (int) ($_ENV['REDIS_PORT'] ?? 6379),
            'password' => $_ENV['REDIS_PASSWORD'] ?? null,
        ],
    ],
];