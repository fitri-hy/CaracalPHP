# CaracalPHP – Database Documentation

Class

```php
Caracal\Core\Database
```

The database layer uses

```php
Illuminate\Database\Capsule\Manager
```

This means CaracalPHP uses **Eloquent ORM (Illuminate Database)** as the database engine.

---

## Purpose of the Database Class

The `Database` class is responsible for initializing and managing the database layer.

Initialize database connections
Configure MySQL or SQLite drivers
Boot the Eloquent ORM
Provide global access to the Capsule manager

---

## Database Configuration

Database settings are loaded from the configuration system.

```php
$config->get('db')
```

Example configuration in `config/config.php`

```php
return [
    'db' => [
        'enabled'   => true,
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'port'      => 3306,
        'name'      => 'caracal',
        'user'      => 'root',
        'pass'      => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]
];
```

---

## Constructor Behavior

Constructor signature

```php
public function __construct(Config $config)
```

Initialization process

Retrieve the `db` configuration
If `enabled` is empty or false, the connection is skipped
Create a Capsule instance
Add the database connection
Set Capsule as the global instance
Boot the Eloquent ORM
Mark the connection as active using `$connected = true`

If the connection fails, the system throws

```text
RuntimeException: Database connection failed
```

---

## Supported Drivers

### MySQL

MySQL is the default driver when the configuration driver is not set to `sqlite`.

### SQLite

If the configuration is set to

```php
'driver' => 'sqlite'
```

The SQLite database file is automatically created at

```
/database/database.sqlite
```

If the file does not exist, it is automatically created using `touch()`.

---

## Checking Database Connection

```php
$db->isConnected();
```

Return value

```
true / false
```

---

## Accessing the Capsule Instance

```php
$db->capsule();
```

This method throws an exception if the database connection is not active.

---

## Recommended Global Access

Use the following static method

```php
Database::connection();
```

Implementation signature

```php
public static function connection(): Capsule
```

This method performs the following steps

Retrieve the `Application` instance
Ensure the database connection is enabled and active
Return the Capsule instance

If the database is disabled, the following exception is thrown

```text
RuntimeException: Database disabled. Set DB_ENABLED=true
```

---

## Example Using Query Builder

```php
use Caracal\Core\Database;

$capsule = Database::connection();

$users = $capsule->table('users')->get();
```

---

## Example Insert Operation

```php
Database::connection()
    ->table('users')
    ->insert([
        'name' => 'John',
        'email' => 'john@example.com'
    ]);
```

---

## Example Using an Eloquent Model

Because `bootEloquent()` is executed during initialization, Eloquent models can be used directly.

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
}
```

Example usage

```php
$users = User::all();
```

---

## Error Handling

If the database connection fails

```
RuntimeException("Database connection failed: ...")
```

If the connection is accessed while the database is disabled

```
RuntimeException("Database disabled. Set DB_ENABLED=true")
```

---

## Method Summary

| Method        | Description                        |
| ------------- | ---------------------------------- |
| __construct() | Initialize the database connection |
| isConnected() | Check the connection status        |
| capsule()     | Retrieve the Capsule instance      |
| connection()  | Provide global access to Capsule   |
