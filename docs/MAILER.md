# CaracalPHP – Mailer Documentation

Class:

```php
Caracal\Core\Mailer
```

Used to send emails via SMTP based on the configuration in `.env` or the application config.

---

Creating an instance:

```php
use Caracal\Core\Mailer;

$mailer = new Mailer();
```

This automatically loads the application configuration (`mail.host`, `mail.user`, `mail.pass`, etc.).

---

Sending an HTML email:

```php
$success = $mailer->send(
    'user@example.com',          // Recipient email
    'Welcome to CaracalPHP',     // Subject
    '<h1>Hello User</h1><p>Your account is ready.</p>' // Email content (HTML)
);

if ($success) {
    echo "Email sent successfully.";
} else {
    echo "Email failed to send.";
}
```

---

Sending a plain text email:

```php
$success = $mailer->send(
    'user@example.com',
    'Plain Text Mail',
    'This is a plain text email.',
    false  // false = plain text
);
```

---

Example in a controller:

```php
namespace App\Modules\Welcome\Controllers;

use Caracal\Core\Controller;
use Caracal\Core\Mailer;

class WelcomeController extends Controller
{
    public function sendMail(): string
    {
        $mailer = new Mailer();

        $success = $mailer->send(
            'user@example.com',
            'Welcome!',
            '<h1>Welcome!</h1><p>This email was sent from CaracalPHP.</p>'
        );

        return $success ? 'Email sent successfully' : 'Email failed to send';
    }
}
```