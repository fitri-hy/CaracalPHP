# CaracalPHP – Config Documentation

Class

```php
Caracal\Core\Config
```

`Config` is responsible for loading and managing application configuration. It loads environment variables, reads configuration files, provides access using dot notation, supports runtime overrides, and allows configuration caching for production environments.

Configuration is typically accessed through the application instance.

```php
Application::getInstance()->config();
```

---

## Retrieving Configuration Values

Use the following method.

```php
get(string $key, mixed $default = null)
```

Example usage

```php
$config->get('app.name');
$config->get('db.host');
$config->get('mail.host');
```

---

## Dot Notation

Configuration keys support dot notation.

Example key

```
app.name
```

This key accesses the following structure internally.

```
$config['app']['name']
```

---

## Checking Configuration Keys

To check whether a configuration key exists

```php
$config->has('app.name');
```

---

## Runtime Override

The `set()` method supports dot notation and allows overriding configuration values during runtime.

```php
$config->set('app.debug', true);
```

---

## Retrieving Environment Variables

Environment variables can be accessed using

```php
$config->env('APP_ENV');
```

---

## Multiple Configuration Files

Configuration files are stored in the following directory.

```
config/
```

Example structure

```
config/
   app.php
   database.php
   cache.php
```

Example file content

```php
return [
    'debug' => true
];
```

The configuration above can be accessed using

```php
$config->get('app.debug');
```

---

## Configuration Cache

In production environments, configuration can be compiled into a single cache file.

```php
$config->cache();
```

Cache file location

```
storage/cache/config.php
```

To remove the cached configuration

```php
$config->clearCache();
```

---

## Retrieving All Configuration

To retrieve the entire configuration array

```php
$config->all();
```

---

## Method Summary

| Method       | Description                       |
| ------------ | --------------------------------- |
| get()        | Retrieve a configuration value    |
| set()        | Override a configuration value    |
| has()        | Check whether a key exists        |
| env()        | Retrieve an environment variable  |
| cache()      | Compile configuration cache       |
| clearCache() | Remove configuration cache        |
| all()        | Retrieve all configuration values |
