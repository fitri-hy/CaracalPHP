# CaracalPHP – Logger Documentation

Class

```php
Caracal\Core\Logger
```

`Logger` is a simple wrapper around **Monolog** used to record:

* Informational logs (`info`)
* Errors (`error`)
* Warnings (`warning`)

Logs are stored in files inside:

```
/storage/logs/
```

---

# Purpose of Logger

The logger provides:

* Structured logging
* File-based log storage
* Support for context arrays
* Monolog as the logging engine

---

# Constructor

```php
public function __construct(string $name = 'app', string $file = 'app.log')
```

### Parameters

| Parameter | Default   | Description         |
| --------- | --------- | ------------------- |
| $name     | 'app'     | Logger channel name |
| $file     | 'app.log' | Log file name       |

---

## Internal Behavior

```php
$this->logger = new MonoLogger($name);
$path = __DIR__ . '/../storage/logs/' . $file;
$this->logger->pushHandler(new StreamHandler($path, MonoLogger::DEBUG));
```

This means:

* All log levels are stored (**DEBUG and above**)
* The log file is automatically placed in `/storage/logs/`
* Logging uses `StreamHandler`

---

# `info()` Method

```php
public function info(string $msg, array $context = []): void
```

Used to record general informational messages.

### Example

```php
use Caracal\Core\Logger;

$logger = new Logger();

$logger->info('User login success', [
    'user_id' => 10
]);
```

---

# `error()` Method

```php
public function error(string $msg, array $context = []): void
```

Used to record application errors.

### Example

```php
$logger->error('Database connection failed', [
    'host' => 'localhost'
]);
```

---

# `warning()` Method

```php
public function warning(string $msg, array $context = []): void
```

Used to record warning messages.

### Example

```php
$logger->warning('API response slow', [
    'duration' => 3.5
]);
```

---

# Log File Location

Default location

```
/core/../storage/logs/app.log
```

Which resolves to

```
project-root/storage/logs/app.log
```

---

# Default Log Format (Monolog)

Example entry inside the log file

```
[2026-03-04T10:15:32.000000+00:00] app.INFO: User login success {"user_id":10} []
```

Structure explanation

| Part      | Meaning                           |
| --------- | --------------------------------- |
| Timestamp | Time when the log was recorded    |
| Channel   | Logger channel name (`app`)       |
| Level     | Log level (`INFO`, `ERROR`, etc.) |
| Message   | Main log message                  |
| Context   | Additional structured data        |
