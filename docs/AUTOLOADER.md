# CaracalPHP – Autoloader Documentation

Class

```php
Caracal\Core\Autoloader
```

`Autoloader` is an automatic class loading system based on PSR-4 style namespace mapping used by CaracalPHP.

This autoloader is responsible for loading class files automatically without requiring manual `require` statements.

---

## Autoloader Purpose

The autoloader has several core responsibilities.

Register the autoload handler with the SPL system
Map namespaces to directories
Convert namespaces into file paths
Load classes automatically
Support class maps and fallback directories

---

## Property Structure

```php
protected array $prefixes = [];
protected array $classMap = [];
protected array $fallbackDirs = [];
```

Explanation

| Property     | Purpose                                             |
| ------------ | --------------------------------------------------- |
| prefixes     | Maps namespaces to directories                      |
| classMap     | Maps specific classes directly to files             |
| fallbackDirs | Fallback directories if no namespace prefix matches |

---

## register()

Method

```php
public function register(): void
```

This method registers the autoloader with the PHP SPL system.

```php
spl_autoload_register([$this, 'loadClass'], true, true);
```

After this method is called, PHP will automatically invoke `loadClass()` whenever a class cannot be found.

This method is typically executed during the framework bootstrap process.

---

## addNamespace()

Method

```php
public function addNamespace(string $prefix, string $baseDir): void
```

This method registers a namespace and maps it to a directory.

Example

```php
$loader->addNamespace('Caracal\\Core', '/core');
$loader->addNamespace('App\\Modules', '/app/Modules');
```

Resulting mapping

```
Caracal\Core\ → /core/
App\Modules\ → /app/Modules/
```

---

## addFallbackDir()

Method

```php
public function addFallbackDir(string $dir): void
```

Adds a fallback directory that will be searched if no registered namespace prefix matches the class.

Example

```php
$loader->addFallbackDir('/lib');
```

If the class cannot be resolved using namespace prefixes, the autoloader will search the fallback directories.

---

## addClassMap()

Method

```php
public function addClassMap(array $map): void
```

Adds direct class-to-file mappings.

Example

```php
$loader->addClassMap([
    'App\\Helpers\\Str' => '/app/Helpers/Str.php'
]);
```

Advantages

Faster lookup
No namespace resolution required

---

## loadClass()

Main autoloader method

```php
public function loadClass(string $class): bool
```

This method is automatically invoked by PHP when a class cannot be found.

Autoloader workflow

Check whether the class exists in `classMap`
Check for a matching namespace prefix
Convert the namespace into a relative path
Combine the path with the base directory
Load the file if it exists
Check fallback directories if the file is not found

---

## Example Autoload Process

Example class

```
App\Modules\User\Controllers\UserController
```

Namespace mapping

```
App\Modules\ → /app/Modules/
```

Autoloader process

Relative class

```
User\Controllers\UserController
```

Converted file path

```
User/Controllers/UserController.php
```

Combined with base directory

```
/app/Modules/User/Controllers/UserController.php
```

If the file exists, the autoloader will execute

```php
require $file;
```

---

## Integration with Application

The autoloader is typically initialized within the `Application` class.

Example

```php
$loader = new Autoloader();
$loader->register();

$loader->addNamespace('Caracal\\Core', __DIR__);
$loader->addNamespace('App\\Modules', $app->path('app/Modules'));
```

With this configuration, the framework can automatically load

Core classes
Module classes

without manual `require` statements.

---

## Recommended Directory Structure

```
app/
 └ Modules/
    └ User/
       ├ Controllers/
       ├ Models/
       └ Views/
```

Namespaces should follow the directory structure.

Example

```
namespace App\Modules\User\Controllers;
```
