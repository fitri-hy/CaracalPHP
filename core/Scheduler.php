<?php
namespace Caracal\Core;

use Symfony\Component\Process\Process;

class Scheduler
{
    protected string $tasksFile;

    public function __construct()
    {
        $dir = __DIR__ . '/../storage/scheduler';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $this->tasksFile = $dir . '/tasks.php';
        if (!file_exists($this->tasksFile)) {
            file_put_contents($this->tasksFile, '<?php return [];');
        }
    }

    public function add(string $name, callable $job, string $cron = 'daily'): void
    {
        $tasks = include $this->tasksFile;
        $tasks[$name] = ['job' => $job, 'cron' => $cron];
        file_put_contents($this->tasksFile, '<?php return ' . var_export($tasks, true) . ';');
    }

    public function runDue(): void
    {
        $tasks = include $this->tasksFile;

        foreach ($tasks as $task) {
            $process = new Process([PHP_BINARY, '-r', 'call_user_func('.var_export($task['job'], true).');']);
            $process->start();
        }
    }
}