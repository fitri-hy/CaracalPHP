<?php
namespace Caracal\Core;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    protected MonoLogger $logger;

    public function __construct(string $name = 'app', string $file = 'app.log')
    {
        $this->logger = new MonoLogger($name);
        $path = __DIR__ . '/../storage/logs/' . $file;
        $this->logger->pushHandler(new StreamHandler($path, MonoLogger::DEBUG));
    }

    public function info(string $msg, array $context = []): void
    {
        $this->logger->info($msg, $context);
    }

    public function error(string $msg, array $context = []): void
    {
        $this->logger->error($msg, $context);
    }

    public function warning(string $msg, array $context = []): void
    {
        $this->logger->warning($msg, $context);
    }
}