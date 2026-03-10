# CaracalPHP – ORM Documentation

Class:

```php
Caracal\Core\ORM
```

`ORM` is an abstract base model that extends the Eloquent `Model` from Illuminate Database.

This class serves as:

* A bridge between CaracalPHP and Eloquent ORM
* A validator to ensure the database is active
* A lightweight wrapper for the query builder

---

Purpose of ORM:

* Use Eloquent without requiring full Laravel
* Ensure the database is active before the model is used
* Provide access to queries via static methods

---

Inheritance:

```php
abstract class ORM extends Model
```

All CaracalPHP models must extend `ORM`:

```php
class User extends ORM
```

They should **not** extend `Model` directly.

---

Protected property:

```php
protected $guarded = [];
```

Explanation:

* All fields are mass assignable
* No default field protection is applied

---

Constructor:

```php
public function __construct(array $attributes = [])
```

Before calling the parent constructor, two validations are performed.

---

Database Active Validation:

```php
$db = Application::getInstance()->db();

if (!$db || !$db->isConnected()) {
    throw new RuntimeException("ORM is used but the database is not active.");
}
```

Meaning:

* ORM cannot be used if the database is disabled
* Or if the database connection fails

---

Capsule Availability Validation:

```php
if (!class_exists(Capsule::class)) {
    throw new RuntimeException("Illuminate Database Capsule is not yet available.");
}
```

Meaning:

* Illuminate Database must be installed
* Capsule must be available

---

Method `table`:

```php
public static function table(): \Illuminate\Database\Query\Builder
```

Returns:

```php
static::query();
```

Used to obtain a query builder for the model.

---

Example Model:

```php
use Caracal\Core\ORM;

class User extends ORM
{
    protected $table = 'users';
}
```

---

Usage Examples

Retrieve all data:

```php
$users = User::all();
```

Query builder via `table()`:

```php
$activeUsers = User::table()
    ->where('status', 'active')
    ->get();
```

Create data:

```php
User::create([
    'name' => 'John',
    'email' => 'john@example.com'
]);
```

Update data:

```php
$user = User::find(1);
$user->update(['name' => 'Updated']);
```

Delete data:

```php
User::destroy(1);
```

---

Eloquent relationships are fully supported:

```php
public function posts()
{
    return $this->hasMany(Post::class);
}
```

---

Important Behavior:

* Database must be active before using any model
* Throws `RuntimeException` if the database is inactive
* Throws `RuntimeException` if Capsule is unavailable
* No connection configuration exists within this class
* Relies on database bootstrap from the Application

---

When Errors Occur:

* `db.enabled = false` in configuration
* Database connection fails
* Illuminate Database is not installed
* Capsule is not bootstrapped