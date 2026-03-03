<?php
namespace Caracal\Core;

use Symfony\Component\Process\Process;
use Ramsey\Uuid\Uuid;

class Queue
{
    protected string $storage;

    public function __construct()
    {
        $this->storage = __DIR__ . '/../storage/jobs';
        if (!is_dir($this->storage)) {
            mkdir($this->storage, 0755, true);
        }
    }

    public function push(callable $job, array $data = []): string
    {
        $id = Uuid::uuid4()->toString();
        $file = $this->storage . '/' . $id . '.job';
        file_put_contents($file, serialize([$job, $data]));
        return $id;
    }

    public function process(string $id): bool
    {
        $file = $this->storage . '/' . $id . '.job';
        if (!file_exists($file)) return false;

        [$job, $data] = unserialize(file_get_contents($file));

        $phpCode = sprintf('call_user_func(%s, %s);', var_export($job, true), var_export($data, true));
        $process = new Process([PHP_BINARY, '-r', $phpCode]);
        $process->start();

        unlink($file);

        return true;
    }

    public function processAll(): void
    {
        foreach (glob($this->storage . '/*.job') as $file) {
            $id = basename($file, '.job');
            $this->process($id);
        }
    }
}