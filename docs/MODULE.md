# CaracalPHP – Module Documentation

Class:

```php
Caracal\Core\Module
```

`Module` is a simple registry used to store a list of modules available in the application.

This class is responsible only for:

* Registering modules
* Storing module paths
* Returning the list of registered modules

It does **not** handle auto-loading or auto-discovery.

---

Purpose of the Module:

* Manage the list of modules centrally
* Ensure module paths are valid
* Provide access to all registered modules

---

Properties:

```php
protected array $modules = [];
```

Data structure:

```php
[
    'ModuleName' => '/absolute/path/to/module'
]
```

---

Method `register`:

```php
public function register(string $name, string $path): void
```

Used to register a new module.

---

Internal validation:

```php
if (!is_dir($path)) {
    throw new \Exception("Module path {$path} not found");
}
```

Explanation:

* The path must be a directory
* If the directory does not exist → an Exception is thrown
* No silent failure occurs

---

Example usage:

```php
use Caracal\Core\Module;

$module = new Module();

$module->register(
    'User',
    __DIR__ . '/app/Modules/User'
);
```

If the folder does not exist:

```text
Exception: Module path /app/Modules/User not found
```

---

Method `all`:

```php
public function all(): array
```

Returns all registered modules.

---

Example:

```php
$modules = $module->all();

print_r($modules);
```

Output:

```php
[
    'User' => '/app/Modules/User',
    'Blog' => '/app/Modules/Blog'
]
```