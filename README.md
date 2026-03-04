<img src="./public/assets/images/welcome.gif" alt="caracal" />

# Caracal PHP

Caracal is a modular, lightweight, and professional PHP framework for building modern web applications with a clean structure, controlled lifecycle, and full built-in features.

---

## Installation

1. **Clone the repository**:

```bash
git clone https://github.com/fitri-hy/CaracalPHP.git
cd CaracalPHP
```

2. **Install dependencies**:

```bash
composer install
```

3. **Configure `.env`** for your environment and database settings.

4. **Run the application**:

```bash
php caracal serve
```

---

# Getting Started with Caracal PHP

This guide is designed to help you understand how to use Caracal PHP.
For more usage details, please see [Full Documentation](./docs).

---

## CLI

```bash
php caracal serve                 # Start server
php caracal serve --port=9000     # Change port
php caracal server:ws             # Start WebSocket server (default port 8080)
php caracal server:ws --port=3000 # Start WebSocket server di port custom
php caracal app:run               # Run application lifecycle
php caracal migrate               # Run all migrations
php caracal migrate:rollback      # Rollback last migration
php caracal migrate:fresh         # Reset & migrate fresh
php caracal db:seed               # Run all seeders
php caracal db:seed UserSeeder    # Run a specific seeder
php caracal migrate --seed        # Migrate & seed simultaneously
php caracal cache:set --key=foo --value=bar --ttl=3600  # Set cache
php caracal cache:clear           # Clear cache
```

---

## Project Structure

```
/CaracalPHP
├── core/                        # Framework engine & core features
│   ├── Application.php          # Application bootstrap & kernel runner
│   ├── Asset.php                # Public asset manager (CSS/JS/images)
│   ├── Autoloader.php           # PSR-4 autoload for core & module classes
│   ├── Cache.php                # Config, route, and view caching
│   ├── Config.php               # Load global & module configurations
│   ├── Controller.php           # Base controller for modules
│   ├── Cookie.php               # HTTP cookie management
│   ├── CSRF.php                 # CSRF protection
│   ├── Database.php             # DB connection & query builder
│   ├── ErrorHandler.php         # Global error & exception handling
│   ├── Event.php                # Event & hooks system
│   ├── Exceptions.php           # Base exception classes
│   ├── Helpers.php              # Utility functions
│   ├── Kernel.php               # Request → response lifecycle
│   ├── Logger.php               # Logging engine
│   ├── Mailer.php               # Email sending engine
│   ├── Middleware.php           # Middleware interface & loader
│   ├── Module.php               # Module loader & registrar
│   ├── ORM.php                  # ORM abstraction for models
│   ├── Plugin.php               # External plugin loader
│   ├── Queue.php                # Background job queue
│   ├── Request.php              # HTTP request abstraction
│   ├── Response.php             # HTTP response abstraction
│   ├── Router.php               # Routing engine
│   ├── Scheduler.php            # Task scheduler / cron jobs
│   ├── Sanitizer.php            # Input sanitization & XSS protection
│   ├── Session.php              # Session management
│   ├── Storage.php              # Private file storage abstraction
│   ├── Upload.php               # Upload file
│   ├── Validation.php           # Data validation engine
│   ├── View.php                 # Template rendering engine
│   └── WebSockets.php           # Websocket
│
├── config/                      # Global & module configuration
│   └── config.php               # Merge core & module configs
│
├── app/                         # Application modules
│   └── Modules/
│       ├── layout.view.php       # Global layout for all modules
│       └── Home/
│           ├── Controllers/HomeController.php
│           ├── Models/HomeModel.php
│           ├── Middleware/HomeMiddleware.php
│           ├── Services/HomeService.php
│           ├── Views/home.view.php
│           └── Routes/web.php
│
├── storage/                     # Private internal storage
│   ├── cache/
│   │   ├── config/
│   │   ├── routes/
│   │   └── views/
│   ├── logs/                     # Application logs
│   │   ├── app.log
│   │   ├── error.log
│   │   └── queue.log
│   ├── sessions/                 # Session files (file driver)
│   └── uploads/                  # Private module uploads
│
├── public/                       # Public assets & entry point
│   ├── index.php                 # Application bootstrap entry
│   ├── .htaccess                 # URL rewrite → index.php
│   ├── robots.txt                # SEO / crawler settings
│   ├── assets/
│   │   ├── css/styles.css
│   │   ├── js/app.js
│   │   └── images/logo.png
│   └── uploads/                  # Public user uploads
│
├── databases/                    # Database related files
│   ├── migrations/               # Migration files
│   │   ├── 20260101_create_users_table.php
│   │   ├── 20260102_create_sessions_table.php
│   │   ├── 20260103_create_jobs_table.php
│   │   └── 20260104_create_cache_table.php
│   ├── seeds/                    # Seeder files
│   │   ├── UserSeeder.php
│   │   └── SessionSeeder.php
│   └── factories/                # Optional: generate test data
│       ├── UserFactory.php
│       └── SessionFactory.php
│
├── caracal                       # CLI entrypoint
├── .env
└── composer.json
```