# CaracalPHP – Plugin Documentation

Class:

```php
Caracal\Core\Plugin
```

`Plugin` is the main extensibility system in CaracalPHP.

This class is responsible for:

* Loading plugin files
* Running plugin lifecycle
* Managing a lightweight service container
* Providing an event hook system

---

Purpose of the Plugin System:

* Make the framework extensible
* Support service registration
* Support singleton services
* Enable event-driven hooks
* Support priority-based execution

---

Internal Structure:

```php
protected array $plugins = [];
protected array $services = [];
protected array $instances = [];
protected array $hooks = [];
```

Explanation:

| Property   | Purpose                   |
| ---------- | ------------------------- |
| $plugins   | Stores plugin definitions |
| $services  | Service registry          |
| $instances | Singleton instances       |
| $hooks     | Event hooks               |

---

Plugin File Format:

* Must be a PHP file
* Must return an array
* Can return an empty array at minimum

Example:

```php
return [
    'priority' => 10,

    'register' => function($plugin) {
        // register services
    },

    'boot' => function($plugin) {
        // boot logic
    },

    'callback' => function($plugin) {
        // optional callback
    }
];
```

---

Method `load`:

```php
public function load(string $path): void
```

Function:

* Include the plugin file
* Validate file existence
* Validate that it returns an array
* Store in `$plugins`

Throws:

```php
throw new \Exception("Plugin file {$path} not found");
```

```php
throw new \Exception("Plugin file must return an array");
```

---

Method `run`:

```php
public function run(): void
```

Executes the plugin lifecycle.

---

Step 1 – Sort by Priority:

```php
usort(...);
```

Higher priority plugins run first. Default priority is 0.

---

Step 2 – Run `register()`:

All plugins call the `register` function first:

```php
$plugin['register']($this);
```

Used to:

* Register services
* Register singletons
* Register hooks

---

Step 3 – Run `boot()` and `callback()`:

After all `register()` executions:

* `boot()`
* `callback()`

Executed in priority order.

---

Service Container:

The plugin system acts as a lightweight service container.

---

Method `set`:

```php
public function set(string $name, $service): void
```

Stores a normal service.

Example:

```php
$plugin->set('logger', new Logger());
```

---

Method `singleton`:

```php
public function singleton(string $name, callable $factory): void
```

Stores a factory; instance is created once.

Example:

```php
$plugin->singleton('mailer', function($plugin) {
    return new Mailer();
});
```

---

Method `get`:

```php
public function get(string $name)
```

Retrieves a service. Behavior:

* If service does not exist → returns `null`
* If singleton → instance is created once
* If callable → executed and returned
* If regular object → returned directly

---

Event Hook System:

Plugins have an internal event system.

---

Method `on`:

```php
public function on(string $event, callable $callback): void
```

Registers an event listener.

Example:

```php
$plugin->on('before_request', function() {
    echo "Request started";
});
```

---

Method `trigger`:

```php
public function trigger(string $event, ...$params): void
```

Executes all callbacks for the event.

Example:

```php
$plugin->trigger('before_request');
```

---

Integration with Kernel:

The kernel triggers these events:

* `before_request`
* `before_dispatch`
* `after_dispatch`
* `response_ready`
* `on_error`
* `after_response`

Plugins can hook into the entire request lifecycle.

---

Method `all`:

```php
public function all(): array
```

Returns all loaded plugins.

---

Complete Plugin Example:

```php
return [

    'priority' => 100,

    'register' => function($plugin) {

        $plugin->singleton('logger', function() {
            return new \Caracal\Core\Logger();
        });

        $plugin->on('before_request', function() {
            echo "Plugin active";
        });
    },

    'boot' => function($plugin) {
        // boot logic
    }
];
```