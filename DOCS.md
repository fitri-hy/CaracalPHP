## Assets

```php
<?= $asset->url('path/file/styles.css') ?>
// Generates the full URL to the asset
// Example: http://localhost:8000/assets/path/file/styles.css
```

## Cache

```php
use Caracal\Core\Application;

// Get the global cache instance from Application
$cache = Application::getInstance()->cache();

// ------------------------------
// Store data in cache
// ------------------------------
$cache->set('site_name', 'Caracal', 3600); // TTL 1 hour
$cache->set('user_123', ['name' => 'John', 'role' => 'admin']); // Default TTL

// ------------------------------
// Retrieve data from cache
// ------------------------------
$siteName = $cache->get('site_name', 'Default Site'); // Default if key not found
$userData = $cache->get('user_123');

// ------------------------------
// Delete a specific cache entry
// ------------------------------
$cache->delete('site_name');

// ------------------------------
// Clear all cache
// ------------------------------
$cache->clearAll();

// ------------------------------
// Get default TTL
// ------------------------------
$ttl = $cache->getDefaultTTL();
```

## Controller

```php
<?php
namespace App\Modules\Home\Controllers;

use Caracal\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $title = 'Welcome to Caracal';
        $content = '<p>Hello, this is the homepage.</p>';

        // Render view 'home/index.php' with data
        return $this->render('home/index.php', [
            'title' => $title,
            'content' => $content
        ]);
    }

    public function greet()
    {
        $name = $this->request->input('name', 'Guest');
        return $this->render('home/greet.php', [
            'title' => 'Greeting Page',
            'content' => "<p>Hello, {$name}!</p>"
        ]);
    }
}
```

## Cookie

```php
use Caracal\Core\Cookie;

// Set basic cookie (string), 60 minutes
Cookie::set('user', 'John Doe', 60);

// Set array cookie (automatically serialized), 120 minutes
Cookie::set('cart', ['item1', 'item2'], 120);

// Read cookie with default
$user = Cookie::get('user', 'Guest');

// Check if a cookie exists
if (Cookie::has('cart')) {
    $cart = Cookie::get('cart');
}

// Delete a cookie
Cookie::delete('user');

// Clear all cookies set by the app
Cookie::clearAll();
```

## CSRF

Render form with CSRF token:

```php
<?php
$csrf = new \Caracal\Core\CSRF();
?>
<form action="/submit" method="post">
    <?= $csrf->inputField() ?>
    <input type="text" name="username">
    <button type="submit">Submit</button>
</form>
```

Validate in controller:

```php
<?php
$csrf = new \Caracal\Core\CSRF();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->checkPost()) {
        die('CSRF token invalid!');
    }

    $username = $_POST['username'];
    echo "Hello, $username";
}
```

## Database

```php
use Caracal\Core\Database;

// Get database connection
$db = Database::connection();

// Get all users from 'users' table
$users = $db->table('users')->get();
foreach ($users as $user) {
    echo $user->name . "\n";
}

// Insert new record
$db->table('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Update record
$db->table('users')->where('id', 1)->update([
    'name' => 'Jane Doe'
]);

// Delete record
$db->table('users')->where('id', 2)->delete();
```

## Error Handler

```php
use Caracal\Core\ErrorHandler;

try {
    include 'Helpers.php'; // file missing → error
} catch (\Throwable $e) {
    $response = ErrorHandler::handle($e);
    echo $response; // display nice error page
}
```

## Event

```php
use Caracal\Core\Event;

// Regular listener
Event::on('user.registered', function($data) {
    echo "New user: " . $data['name'] . PHP_EOL;
});

// One-time listener
Event::once('user.registered', function($data) {
    echo "Welcome email sent to " . $data['email'] . PHP_EOL;
});

// Trigger event
Event::trigger('user.registered', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Remove specific listener
// Event::off('user.registered', $callback);

// Remove all listeners
// Event::off('user.registered');
```

## Exception

```php
<?php
use Caracal\Core\ValidationException;

try {
    throw new ValidationException(
        "Invalid email",       // Error message
        422,                   // Optional code
        ['email' => 'invalid format'] // Additional context
    );
} catch (ValidationException $e) {
    echo $e->getMessage() . PHP_EOL;
    print_r($e->getContext());
}
```

## Helpers

```php
<?php
use Caracal\Core\Helpers;

// Debug variable
$variable = ['name' => 'John', 'role' => 'Admin'];
Helpers::dd($variable); // var_dump + exit

// Environment variable
$appKey = Helpers::env('APP_KEY', 'default_key');

// Generate application URL
$urlHome  = Helpers::url('/');       // http://localhost/
$urlAbout = Helpers::url('/about');  // http://localhost/about
```

## Logger

```php
<?php
use Caracal\Core\Logger;

$log = new Logger('app', 'app.log');

// Log info
$log->info("Application started");

// Log warning with context
$log->warning("User not allowed", ['user_id' => 123]);

// Log error with exception context
try {
    throw new Exception("Fatal error occurred");
} catch (Exception $e) {
    $log->error("Exception caught", [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
    ]);
}
```

## Mailer

```php
<?php
use Caracal\Core\Mailer;

$mail = new Mailer();

// Send email
if ($mail->send('user@example.com', 'Hello from Caracal', '<p>Email body</p>')) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check storage/logs/mailer.log";
}
```

## Middleware

```php
<?php
namespace App\Modules\Home\Middleware;

use Caracal\Core\MiddlewareInterface;
use Caracal\Core\Request;
use Caracal\Core\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $loggedIn = false; // Example: replace with real condition

        if (!$loggedIn) {
            return new Response('Unauthorized', 401);
        }

        return $next($request);
    }
}
```

## ORM

```php
<?php
namespace App\Modules\Home\Models;

use Caracal\Core\ORM;

class User extends ORM
{
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
}

use App\Modules\Home\Models\User;

// CREATE
$user = new User(['name' => 'John Doe', 'email' => 'john@example.com']);
$user->save();

// READ all
$users = User::all();
foreach ($users as $u) {
    echo $u->name . ' - ' . $u->email . PHP_EOL;
}

// READ single
$john = User::where('email', 'john@example.com')->first();
echo "Hello, " . $john->name . PHP_EOL;

// UPDATE
$john->name = 'John D';
$john->save();

// DELETE
$john->delete();
```

## Plugin

```php
// plugins/example.php

<?php

return [

    'name' => 'ExamplePlugin',

    'boot' => function () {
        echo "Example Plugin loaded";
    }

];
```

## Queue

```php
use Caracal\Core\Queue;

$queue = new Queue();

// Add job
$jobId = $queue->push(function($data) {
    echo "Processing job: " . $data['task'] . PHP_EOL;
}, ['task' => 'Send email']);

// Process job by ID
$queue->process($jobId);

// Or process all jobs
$queue->processAll();
```

## Request

```php
use Caracal\Core\Request;

$request = Request::capture();

$method = $request->method(); // GET, POST, etc.
$uri = $request->uri();       // /home
$name = $request->input('name', 'default name');
$data = $request->all();
```

## Response

```php
use Caracal\Core\Response;

// HTML response
$htmlResponse = new Response('<h1>Welcome!</h1>');
$htmlResponse->send();

// JSON response
$jsonResponse = Response::json([
    'status' => 'ok',
    'message' => 'Data sent successfully'
]);
$jsonResponse->send();

// Redirect
$redirect = Response::redirect('/home');
$redirect->send();
```

## Route

```php
<?php

use App\Modules\Posts\Controllers\PostController;

return [
    ['GET', '/api/posts', 'PostController@index'],        
    ['GET', '/api/posts/{id}', 'PostController@show'],    
    ['POST', '/api/posts', 'PostController@store'],       
    ['PUT', '/api/posts/{id}', 'PostController@update'],  
    ['DELETE', '/api/posts/{id}', 'PostController@delete'],
];
```

## Sanitizer

```php
<?php
use Caracal\Core\Sanitizer;

// Allow iframe from common video domains
$sanitizer = new Sanitizer([
    'www.youtube.com/embed',
    'player.vimeo.com/video',
    'www.dailymotion.com/embed/video'
]);

$input = '<script>alert("xss")</script><p>Hello World!</p>';
$clean = $sanitizer->clean($input); // <p>Hello World!</p>

$postData = [
    'name' => '<b>John</b>',
    'bio' => '<iframe src="https://www.youtube.com/embed/xyz"></iframe><script>alert(1)</script>'
];

$cleanData = $sanitizer->cleanArray($postData);
print_r($cleanData);
```

## Scheduler

```php
<?php
use Caracal\Core\Scheduler;

$scheduler = new Scheduler();

$scheduler->add('daily-report', function() {
    echo "Running daily report...\n";
}, 'daily');

$scheduler->add('ping-server', function() {
    echo "Ping server successful: " . date('H:i:s') . "\n";
}, 'everyMinute');

$scheduler->runDue();
```

## Session

```php
<?php
use Caracal\Core\Session;

$session = new Session();

$session->set('user_id', 123);
echo $session->get('user_id');

if ($session->has('user_id')) echo "User logged in";

$session->remove('user_id');

$session->flash('success', 'Data saved!');
echo $session->flash('success'); // "Data saved!"
echo $session->flash('success'); // null

echo $session->id();
$session->clear();
```

## Storage

```php
<?php
use Caracal\Core\Storage;

$storage = new Storage();

$storage->put('hello.txt', 'Hello World!');
echo $storage->get('hello.txt');

if ($storage->exists('hello.txt')) echo "File exists!";

echo $storage->path('hello.txt');

$storage->delete('hello.txt');
$storage->makeDir('images');
$storage->put('images/pic1.png', '...binary data...');
```

## Validation

```php
<?php
use Caracal\Core\Validation;
use Respect\Validation\Validator as v;

$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 17,
];

$rules = [
    'name'  => v::stringType()->notEmpty()->length(3, 50),
    'email' => v::email(),
    'age'   => v::intType()->min(18),
];

$validator = new Validation();

if ($validator->validate($data, $rules)) {
    echo "Validation passed";
} else {
    echo "Validation failed";
    print_r($validator->errors());
}
```

## Views

```php
app/Modules/
├── layout.view.php
└── Home/
    └── Views/
        └── home.view.php

// Controller
return $this->render('Home/Views/home.view.php', [
    'title'   => 'Home',
    'message' => $message,
    'users'   => $users,
]);
```
