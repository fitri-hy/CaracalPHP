# CaracalPHP – Session Documentation

Class:

```php
Caracal\Core\Session
```

`Session` is a **secure and flexible session engine** for CaracalPHP.

It is designed to:

* Be production-safe
* Support **multi-driver storage**
* Use **AES-256 encryption**
* Support **flash messages**
* Support **lazy session start** (sessions only start when used)

All session data is **encrypted and HMAC-signed**, preventing tampering.

---

Features:

| Feature              | Status |
| -------------------- | ------ |
| AES-256 Encryption   | ✅      |
| HMAC Data Integrity  | ✅      |
| Flash Message        | ✅      |
| Lazy Session Start   | ✅      |
| Redis Driver         | ✅      |
| Database Driver      | ✅      |
| Secure Cookie        | ✅      |
| Session Regeneration | ✅      |

---

Supported Drivers:

| Driver     | Description                           |
| ---------- | ------------------------------------- |
| `file`     | Default, stored in `storage/sessions` |
| `redis`    | Stored in Redis server                |
| `database` | Stored in `sessions` table            |

---

Configuration (.env):

```dotenv
SESSION_DRIVER=file
SESSION_LIFETIME=7200
SESSION_COOKIE=caracal_session

APP_KEY=your-secret-key
```

Explanation:

| Variable         | Description                 |
| ---------------- | --------------------------- |
| SESSION_DRIVER   | file / redis / database     |
| SESSION_LIFETIME | Session lifetime in seconds |
| SESSION_COOKIE   | Name of the session cookie  |
| APP_KEY          | Session encryption key      |

---

Initialization:

```php
use Caracal\Core\Session;

$session = new Session();
```

> No need to call `session_start()`.

Sessions use **lazy start**, which means a session begins only when calling:

```
set(), get(), flash(), id()
```

---

Basic Usage:

Set session data:

```php
$session->set('user_id', 123);
```

Get session data:

```php
$userId = $session->get('user_id');
```

With default value:

```php
$role = $session->get('role', 'guest');
```

Check if a key exists:

```php
if ($session->has('user_id')) {
    echo "User logged in";
}
```

Remove session data:

```php
$session->remove('user_id');
```

---

Flash Messages:

Flash data is available **for the next request only**.

Typically used for:

* Success notifications
* Error messages
* UI alerts

Set flash data:

```php
$session->flash('success', 'Data saved successfully');
```

Get flash data:

```php
$message = $session->flash('success');
```

Lifecycle:

```
Request 1 -> set flash
Request 2 -> flash available
Request 3 -> flash gone
```

---

Session ID:

```php
$id = $session->id();
```

Regenerate session ID (prevents session fixation):

```php
$session->regenerate();
```

Retrieve all session data:

```php
$data = $session->all();
```

Returns all session data (flash excluded). All data is automatically decrypted.

Clear session:

```php
$session->clear();
```

Removes session data, destroys the session ID, and deletes the session cookie.

---

Encryption:

All session data uses:

```
AES-256-CBC encryption
HMAC-SHA256 signing
```

Payload structure:

```
base64(
   IV
   HMAC
   CIPHERTEXT
)
```

Benefits:

* Cannot be read directly
* Cannot be modified
* Integrity guaranteed

If the payload is corrupted:

```
get() will return the default value
```

---

Redis Driver:

```dotenv
SESSION_DRIVER=redis
```

Stored as:

```
caracal_session:{session_id}
```

Example:

```
caracal_session:2f1d8a73a9e5c1
```

> Redis does **not** use `flushdb()`, safe for multi-application usage.

---

Database Driver:

```dotenv
SESSION_DRIVER=database
```

Automatically creates `sessions` table:

```sql
id VARCHAR PRIMARY KEY
payload TEXT
expires INT
```

Expired sessions are automatically deleted by garbage collection.

---

File Driver (Default):

Stored in:

```
storage/sessions
```

Suitable for:

* Development
* Small applications
* Single server deployment

---

Security:

### Encryption

All session data is encrypted using:

```
AES-256-CBC
```

### HMAC Verification

Payload is signed using:

```
HMAC-SHA256
```

Tampered payloads will fail decryption.

### Secure Cookies

Default cookie settings:

```
HttpOnly
SameSite=Lax
Secure (HTTPS)
```

* Not accessible via JavaScript
* Partially protected from CSRF
* Secure over HTTPS

---

Example: Login Flow

```php
$session = new \Caracal\Core\Session();

// set user session
$session->set('user_id', $user->id);
$session->set('role', $user->role);

// regenerate ID
$session->regenerate();

// set flash message
$session->flash('success', 'Welcome back!');

header('Location: /dashboard');
exit;
```

On dashboard:

```php
$session = new Session();

if ($msg = $session->flash('success')) {
    echo "<div class='alert'>$msg</div>";
}
```

---

Summary:

| Capability           | Supported |
| -------------------- | --------- |
| Multi Driver Session | ✅         |
| Encryption           | ✅         |
| Flash Message        | ✅         |
| Redis Support        | ✅         |
| Database Session     | ✅         |
| Lazy Start           | ✅         |
| Secure Cookies       | ✅         |
