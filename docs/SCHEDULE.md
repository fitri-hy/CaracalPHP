# CaracalPHP – Scheduler Documentation

Class:

```php
Caracal\Core\Scheduler
```

`Scheduler` is a file-based task scheduling system that allows you to run commands asynchronously using the system cron.

It is designed to be:

* Lightweight
* Database-free
* Redis-free
* Suitable for shared hosting
* Production-ready for modular applications

---

How Scheduler Works:

```
1. Task is registered via Scheduler::add()
2. Task is saved in storage/scheduler/tasks.php
3. System cron calls the scheduler runner
4. Scheduler checks for tasks that are "due"
5. Task is executed as a separate process (async)
```

---

Storage Location:

Scheduler automatically creates:

```text
storage/scheduler/
```

Automatically created files:

```text
storage/scheduler/tasks.php
storage/scheduler/scheduler.log
```

No manual creation is required.

---

Adding a Task:

```php
public function add(
    string $name,
    string $command,
    string $cron = '* * * * *',
    bool $enabled = true,
    bool $preventOverlap = true
): void
```

Example:

```php
use Caracal\Core\Scheduler;

$scheduler = new Scheduler();

$scheduler->add(
    'cleanup',
    'artisan cleanup.php',
    'daily'
);
```

---

Cron Expression Support:

### Presets

| Preset  | Cron Equivalent |
| ------- | --------------- |
| hourly  | 0 * * * *       |
| daily   | 0 0 * * *       |
| weekly  | 0 0 * * 0       |
| monthly | 0 0 1 * *       |

Example:

```php
$scheduler->add('report', 'artisan report.php', 'weekly');
```

---

### Custom Cron Expression

Standard format:

```
* * * * *
│ │ │ │ │
│ │ │ │ └─ Day of week (0-6)
│ │ │ └─── Month (1-12)
│ │ └───── Day of month (1-31)
│ └─────── Hour (0-23)
└───────── Minute (0-59)
```

Example every 5 minutes:

```php
$scheduler->add(
    'send-mails',
    'artisan send:emails',
    '*/5 * * * *'
);
```

---

Running Due Tasks:

```php
$scheduler->runDue();
```

Typically called from a CLI script:

```php
// schedule.php

require 'vendor/autoload.php';

use Caracal\Core\Scheduler;

$scheduler = new Scheduler();
$scheduler->runDue();
```

---

System Cron Integration (Linux):

Add to crontab:

```bash
* * * * * php /path-to-project/schedule.php
```

Scheduler will run every minute and execute tasks that are due.

---

Manual Task Execution:

```php
$scheduler->run('cleanup');
```

Throws an exception if the task is not found.

---

Prevent Overlapping:

If `preventOverlap = true`:

* Scheduler creates a lock file
* Task will not run if already executing
* Prevents duplicate execution

Example:

```php
$scheduler->add(
    'backup',
    'artisan backup.php',
    'daily',
    true,
    true
);
```

---

Enable / Disable Task:

You can disable a task when registering it:

```php
$scheduler->add(
    'temporary-task',
    'artisan temp.php',
    'daily',
    false
);
```

Tasks with `enabled = false` will not be executed.

---

Logging:

All executed tasks are logged in:

```text
storage/scheduler/scheduler.log
```

Example log entry:

```
[2026-03-04 00:00:00] Task [cleanup] started.
```

---

Tasks Configuration File (`tasks.php`):

Automatically created and contains task configurations:

```php
<?php return [
    'cleanup' => [
        'command' => 'artisan cleanup.php',
        'cron' => '0 0 * * *',
        'enabled' => true,
        'prevent_overlap' => true,
        'last_run' => 1700000000
    ]
];
```

Manual editing of this file is not required.