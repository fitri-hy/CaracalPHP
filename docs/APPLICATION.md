# CaracalPHP – Application Documentation

Class

```php
Caracal\Core\Application
```

`Application` is the core container of CaracalPHP. It is responsible for bootstrapping the framework, loading configuration, providing the service container, initializing the database, loading plugins, and running the HTTP kernel.

---

## Application Role

`Application` acts as the central system of the framework with several main responsibilities.

Core service container
Framework bootstrap
Plugin system host
Entry point for the request lifecycle
Global singleton instance

The application object is the primary interface used to access services such as configuration, cache, database, and the plugin manager.

---

## Singleton Instance

`Application` uses the Singleton pattern so that only one instance exists during runtime.

Method

```php
public static function getInstance(): self
```

Usage

```php
use Caracal\Core\Application;

$app = Application::getInstance();
```

---

## Initialization Lifecycle

When the `Application` instance is created, the constructor performs the following bootstrap process.

Determine the project base path
Register the autoloader
Load configuration
Set the timezone
Register base services
Bootstrap required services
Initialize the plugin manager
Load plugins from the plugin directory
Execute plugins
Create the Kernel instance

This sequence ensures that all core components are ready before processing any request.

---

## registerCore()

```php
protected function registerCore(): void
```

This method registers the autoloader and the main framework namespaces.

Registered namespaces

| Namespace    | Directory    |
| ------------ | ------------ |
| Caracal\Core | /core        |
| App\Modules  | /app/Modules |

The autoloader resolves classes based on these namespaces.

Example module structure

```
app/
 └ Modules/
    └ User/
       └ UserController.php
```

---

## Service Container

The application provides a lightweight container for managing dependencies and services.

Container properties

```php
protected array $bindings = [];
protected array $instances = [];
```

The container supports three main operations.

bind
singleton
make

---

### bind

Registers a service in the container.

```php
$app->bind(Service::class, function ($app) {
    return new Service();
});
```

Each call to `make()` will create a new instance.

---

### singleton

Registers a service that is created only once.

```php
$app->singleton(Logger::class, function ($app) {
    return new Logger();
});
```

The instance is stored in `$instances`.

---

### make

Retrieves a service instance from the container.

```php
$service = $app->make(Service::class);
```

If the service has not been instantiated yet, the resolver will be executed.

---

## Base Services

Base services are registered in the following method.

```php
protected function registerBaseServices()
```

Available built-in services

| Service | Description            |
| ------- | ---------------------- |
| cache   | Cache instance         |
| config  | Configuration instance |
| db      | Database connection    |
| plugins | Plugin manager         |

These services can be accessed through helper methods provided by the `Application` class.

---

## Config Service

The configuration service is used to access application settings.

Method

```php
$app->config()
```

Example

```php
$debug = $app->config()->get('app.debug');
```

The configuration system loads values from configuration files and environment variables.

---

## Database Service

The database is initialized only if the following configuration is enabled.

```
db.enabled = true
```

If the database connection fails, the `$database` property will contain `null`.

Accessing the database

```php
$db = $app->db();
```

Return values

| Value             | Condition                              |
| ----------------- | -------------------------------------- |
| Database instance | Connection successful                  |
| null              | Database disabled or connection failed |

---

## Cache Service

The cache service is resolved lazily through the container.

Method

```php
$app->cache()
```

Example

```php
$app->cache()->set('key', 'value');
$value = $app->cache()->get('key');
```

The cache instance is created only when it is first requested.

---

## Plugin System

The plugin manager is stored in the following property.

```php
protected Plugin $plugin;
```

It can be accessed through

```php
$app->plugins();
```

The plugin manager is responsible for loading and executing framework plugins.

---

## Plugin Directory

Plugins are automatically loaded from the following directory.

```
/plugins
```

All `.php` files in this directory will be loaded.

```php
$this->plugin->load($file);
```

After loading, plugins are executed during the application boot process.

---

## Boot Process

Method

```php
protected function boot()
```

The boot process ensures that plugins are executed only once during runtime.

If the application has already been booted, the method will not run again.

---

## Path Helper

Method

```php
public function path(string $path = ''): string
```

This method generates an absolute path relative to the project root.

Example

```php
$app->path('plugins');
```

Result

```
{projectRoot}/plugins
```

---

## Base Path

Method

```php
public function basePath(): string
```

Returns the root directory of the project.

Example

```php
$root = $app->basePath();
```

---

## Running the Application

Main method used to run the framework

```php
public function run(): void
```

This method calls the kernel.

```php
$this->kernel->handle();
```

It is typically used in the application entry point such as `public/index.php`.

Example

```php
use Caracal\Core\Application;

$app = Application::getInstance();
$app->run();
```

---

## Core Properties

| Property   | Description                 |
| ---------- | --------------------------- |
| $config    | Configuration instance      |
| $kernel    | HTTP Kernel                 |
| $database  | Database connection         |
| $plugin    | Plugin manager              |
| $basePath  | Project root                |
| $bindings  | Service container bindings  |
| $instances | Singleton service instances |

---

## Available Services

| Service  | Access Method     |
| -------- | ----------------- |
| Config   | `$app->config()`  |
| Database | `$app->db()`      |
| Cache    | `$app->cache()`   |
| Plugin   | `$app->plugins()` |
| Kernel   | `$app->kernel`    |

---

## Usage Examples

Retrieve configuration

```php
$app = Application::getInstance();

$env = $app->config()->get('app.env');
```

Use cache

```php
$app->cache()->set('user', $data);
```

Use database

```php
$db = $app->db();
```

Register and resolve a service

```php
$app->singleton(UserService::class, function ($app) {
    return new UserService();
});

$userService = $app->make(UserService::class);
```
