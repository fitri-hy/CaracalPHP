<?php
namespace Caracal\Core;

use Symfony\Component\Process\Process;

class Scheduler
{
    protected string $tasksFile;
    protected string $logFile;

    public function __construct()
    {
        $dir = __DIR__ . '/../storage/scheduler';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->tasksFile = $dir . '/tasks.php';
        $this->logFile   = $dir . '/scheduler.log';

        if (!file_exists($this->tasksFile)) {
            file_put_contents($this->tasksFile, '<?php return [];');
        }
    }

    public function add(
        string $name,
        string $command,
        string $cron = '* * * * *',
        bool $enabled = true,
        bool $preventOverlap = true
    ): void {

        $tasks = include $this->tasksFile;

        $tasks[$name] = [
            'command' => $command,
            'cron' => $this->normalizeCron($cron),
            'enabled' => $enabled,
            'prevent_overlap' => $preventOverlap,
            'last_run' => null
        ];

        $this->save($tasks);
    }

    public function runDue(): void
    {
        $tasks = include $this->tasksFile;
        $now   = time();

        foreach ($tasks as $name => &$task) {

            if (!$task['enabled']) continue;

            if (!$this->isDue($task['cron'], $now)) continue;

            if ($task['prevent_overlap'] && $this->isRunning($name)) {
                continue;
            }

            $this->runTask($name, $task);

            $task['last_run'] = $now;
        }

        $this->save($tasks);
    }

    public function run(string $name): void
    {
        $tasks = include $this->tasksFile;

        if (!isset($tasks[$name])) {
            throw new \Exception("Task {$name} not found.");
        }

        $this->runTask($name, $tasks[$name]);
    }

    protected function runTask(string $name, array $task): void
    {
        $lockFile = $this->getLockFile($name);

        file_put_contents($lockFile, getmypid());

        $process = Process::fromShellCommandline(
            PHP_BINARY . ' ' . $task['command']
        );

        $process->start();

        $this->log("Task [{$name}] started.");

        unlink($lockFile);
    }

    protected function normalizeCron(string $cron): string
    {
        return match ($cron) {
            'hourly' => '0 * * * *',
            'daily' => '0 0 * * *',
            'weekly' => '0 0 * * 0',
            'monthly' => '0 0 1 * *',
            default => $cron
        };
    }

    protected function isDue(string $cron, int $timestamp): bool
    {
        [$min, $hour, $day, $month, $week] = explode(' ', $cron);

        $date = getdate($timestamp);

        return $this->match($min, $date['minutes'])
            && $this->match($hour, $date['hours'])
            && $this->match($day, $date['mday'])
            && $this->match($month, $date['mon'])
            && $this->match($week, $date['wday']);
    }

    protected function match(string $pattern, int $value): bool
    {
        if ($pattern === '*') return true;
        return (int)$pattern === $value;
    }

    protected function isRunning(string $name): bool
    {
        return file_exists($this->getLockFile($name));
    }

    protected function getLockFile(string $name): string
    {
        return dirname($this->tasksFile) . "/{$name}.lock";
    }

    protected function save(array $tasks): void
    {
        file_put_contents(
            $this->tasksFile,
            '<?php return ' . var_export($tasks, true) . ';'
        );
    }

    protected function log(string $message): void
    {
        file_put_contents(
            $this->logFile,
            '[' . date('Y-m-d H:i:s') . "] {$message}\n",
            FILE_APPEND
        );
    }
}