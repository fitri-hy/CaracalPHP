# 📘 CaracalPHP – Kernel Documentation

Class:

```php
Caracal\Core\Kernel
```

Kernel adalah **entry point utama request lifecycle** di CaracalPHP.

Ia bertanggung jawab untuk:

* Menjalankan plugin hooks
* Menangani request
* Dispatch ke router
* Menangani error
* Mengirim response
* Preload cache sistem

---

# 🎯 Peran Kernel

Kernel mengatur alur berikut:

```
before_request
→ preloadCache
→ before_dispatch
→ router dispatch
→ after_dispatch
→ response_ready
→ send response
→ after_response
```

Jika terjadi error:

```
on_error
→ ErrorHandler::handle()
→ send response 500
```

---

# 1️⃣ Constructor

```php
public function __construct(Application $app)
```

Kernel membutuhkan instance `Application`.

Biasanya dipanggil dari front controller (misalnya `public/index.php`):

```php
$app = new Application();
$kernel = new Kernel($app);
$kernel->handle();
```

---

# 2️⃣ Method `handle()`

```php
public function handle(): void
```

Method ini menjalankan seluruh lifecycle request.

---

## Urutan Eksekusi Detail

### 1. Ambil Plugin Manager

```php
$plugins = $this->app->plugins();
```

---

### 2. Trigger `before_request`

```php
$plugins->trigger('before_request');
```

Digunakan untuk:

* Logging awal
* Setup environment
* Custom boot logic

---

### 3. Capture Request

```php
$request = Request::capture();
```

---

### 4. Inisialisasi Router

```php
$router = new Router($this->app);
```

---

### 5. Preload Cache

```php
$this->preloadCache($router);
```

(Lihat bagian preloadCache di bawah)

---

### 6. Trigger `before_dispatch`

```php
$plugins->trigger('before_dispatch', $request);
```

---

### 7. Dispatch Route

```php
$response = $router->dispatch($request);
```

---

### 8. Trigger `after_dispatch`

```php
$plugins->trigger('after_dispatch', $response);
```

---

### 9. Pastikan Response Instance

Jika controller tidak mengembalikan `Response`:

```php
$response = new Response((string) $response);
```

---

### 10. Trigger `response_ready`

```php
$plugins->trigger('response_ready', $response);
```

---

# 3️⃣ Error Handling

Jika terjadi exception:

```php
catch (\Throwable $e)
```

Urutan:

1. Trigger `on_error`
2. Jalankan:

```php
$response = ErrorHandler::handle($e);
```

3. Pastikan instance `Response`
4. Kirim status 500 jika perlu

---

# 4️⃣ Kirim Response

```php
$response->send();
```

Setelah itu:

```php
$plugins->trigger('after_response', $response);
```

---

# 5️⃣ preloadCache()

Method:

```php
protected function preloadCache(Router $router): void
```

Berfungsi untuk menyimpan cache awal jika belum ada.

---

## Cache yang Dipreload

### 1️⃣ Routes

```php
$cache->get('routes')
```

Jika tidak ada:

```php
$routes = $router->loadRoutes();
$cache->set('routes', $routes);
```

---

### 2️⃣ Layout

File dicek di:

```
app/Modules/layout.view.php
```

Jika ada → disimpan sebagai string.

---

### 3️⃣ Middleware

Disimpan sebagai array kosong jika belum ada.

---

### 4️⃣ Plugins

Disimpan sebagai array kosong jika belum ada.

---

### 5️⃣ Events

Disimpan sebagai array kosong jika belum ada.

---

# 📌 Lifecycle Summary

| Tahap                    | Trigger         |
| ------------------------ | --------------- |
| Awal request             | before_request  |
| Sebelum dispatch         | before_dispatch |
| Setelah dispatch         | after_dispatch  |
| Response siap            | response_ready  |
| Error terjadi            | on_error        |
| Setelah response dikirim | after_response  |