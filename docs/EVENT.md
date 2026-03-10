# CaracalPHP – Event Documentation

Class

```php
Caracal\Core\Event
```

The event system is a simple static event dispatcher that supports:

Regular event listeners (`on`)
One-time listeners (`once`)
Event triggering
Removing listeners (`off`)

---

## Core Concept

The event system follows this pattern.

```
Register Listener → Trigger Event → Execute Callback
```

All methods are static, so no instance creation is required.

---

## Registering an Event Listener

Use the following method.

```php
Event::on(string $event, callable $callback);
```

### Example

```php
use Caracal\Core\Event;

Event::on('user.registered', function ($data) {
    return 'User registered: ' . $data['email'];
});
```

This listener will run every time the event is triggered.

---

## One-Time Listener

Use the following method.

```php
Event::once(string $event, callable $callback);
```

This listener behaves as follows.

Executed only once
Automatically removed after execution

### Example

```php
Event::once('user.login', function ($data) {
    return 'First login detected';
});
```

---

## Triggering an Event

Use the following method.

```php
Event::trigger(string $event, array $data = []): array
```

### Example

```php
$results = Event::trigger('user.registered', [
    'email' => 'john@example.com'
]);
```

Return value

```php
array
```

The returned array contains the results from each callback.

Example result

```php
[
    'User registered: john@example.com'
]
```

---

## Removing Event Listeners

Use the following method.

```php
Event::off(string $event, ?callable $callback = null);
```

### Removing All Listeners for an Event

```php
Event::off('user.registered');
```

This removes

Regular listeners
One-time listeners

---

### Removing a Specific Listener

```php
$callback = function ($data) {
    return 'Test';
};

Event::on('test.event', $callback);

Event::off('test.event', $callback);
```

Only the specified callback will be removed.

---

## Internal Behavior

Listeners are stored in two static arrays.

```php
protected static array $listeners = [];
protected static array $onceListeners = [];
```

When `trigger()` is executed, the following process occurs.

Execute all `$listeners`
Execute all `$onceListeners`
Remove `$onceListeners` for that event
Return the array of callback results

---

## Example Usage in an Application

### Example: After User Registration

```php
Event::on('user.registered', function ($data) {
    // send email
});

Event::trigger('user.registered', [
    'email' => 'john@example.com'
]);
```

---

### Example: Query Logging

```php
Event::on('db.query.executed', function ($data) {
    file_put_contents('log.txt', $data['query'] . PHP_EOL, FILE_APPEND);
});
```

---

## Important Behavior

Listeners are stored in a static global state
Listeners receive a single `$data` parameter (array)
Callback return values are collected into an array
`once()` listeners are automatically removed after execution
`off()` can remove all listeners or a specific callback

---

## Important Considerations

Listeners are compared using `===`
Closures with different instances are not considered identical
Event names are string literals and case-sensitive

---

## Method Summary

| Method    | Description                  |
| --------- | ---------------------------- |
| on()      | Register an event listener   |
| once()    | Register a one-time listener |
| trigger() | Execute an event             |
| off()     | Remove a listener            |
