# CaracalPHP – Queue Documentation

Class:

```php
Caracal\Core\Queue
```

`Queue` is a simple file-based job queue system that allows you to:

* Store jobs in persistent storage
* Run jobs asynchronously
* Process a single job or all jobs

This queue **does not use a database or Redis**, but instead relies on `.job` files.

---

Purpose of the Queue:

* Run heavy tasks in the background
* Avoid blocking HTTP requests
* Provide a lightweight queue system without external services

---

Job Storage Location:

All jobs are stored in:

```
/storage/jobs
```

If the folder does not exist, it is automatically created:

```php
mkdir($this->storage, 0755, true);
```

---

Method `push`:

```php
public function push(callable $job, array $data = []): string
```

Used to add a job to the queue.

---

How it Works:

1. Generate a UUID v4
2. Create a file `{uuid}.job`
3. Store a serialized array containing:

   * Callable
   * Data

```php
serialize([$job, $data]);
```

---

Return Value:

* Returns the job ID (UUID string)

---

Example:

```php
use Caracal\Core\Queue;

$queue = new Queue();

$jobId = $queue->push(function ($data) {
    file_put_contents('log.txt', $data['message'] . PHP_EOL, FILE_APPEND);
}, [
    'message' => 'Hello Queue'
]);

echo "Job ID: " . $jobId;
```

---

Method `process`:

```php
public function process(string $id): bool
```

Used to process a single job by its ID.

---

Internal Workflow:

1. Check if the job file exists
2. Unserialize the file
3. Generate inline PHP code
4. Execute using Symfony Process:

```php
new Process([PHP_BINARY, '-r', $phpCode]);
```

5. Delete the job file after execution

---

Important:

The process runs with:

```php
$process->start();
```

Meaning:

* Non-blocking
* Asynchronous
* Does not wait for completion

---

Return Value:

* `true` → if the job exists and is executed
* `false` → if the job file does not exist

---

Method `processAll`:

```php
public function processAll(): void
```

Processes all `.job` files in the storage folder.

---

Internal Workflow:

```php
foreach (glob('*.job') as $file)
```

Then call:

```php
$this->process($id);
```

---

Example Worker Script:

Create a CLI file:

```php
$queue = new Queue();
$queue->processAll();
```

Can be executed via cron:

```
* * * * * php worker.php
```

---

Job File Format:

The `.job` file contains:

```php
serialize([$job, $data]);
```

Example after unserialize:

```php
[
    Closure,
    ['message' => 'Hello Queue']
]
```