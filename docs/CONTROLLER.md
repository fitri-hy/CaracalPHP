# CaracalPHP – Base Controller Documentation

Class dasar controller:

```
Caracal\Core\Controller
```

Semua controller module sebaiknya mewarisi class ini.

Controller menyediakan akses langsung ke:

* Request
* View renderer
* Asset helper
* Config
* Cache

Controller dirancang agar **controller module tetap sederhana dan bersih**.

---

# Properti yang Tersedia

Saat controller di-extend, properti berikut otomatis tersedia.

```
protected Request $request
protected View $view
protected Asset $asset
protected Application $app
```

Properti ini diinisialisasi pada constructor:

```php
$this->app     = Application::getInstance();
$this->request = Request::capture();
$this->view    = new View();
$this->asset   = new Asset();
```

---

# Menggunakan Controller di Module

Lokasi controller:

```
app/Modules/{ModuleName}/Controllers/
```

Contoh controller:

```php
namespace App\Modules\Home\Controllers;

use Caracal\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->view(
            'Home/Views/home.view.php',
            [
                'title' => 'Homepage'
            ]
        );
    }
}
```

---

# Render View

Method utama untuk menampilkan halaman HTML:

```
view(string $template, array $data = [], bool $layout = true): string
```

Parameter:

| Parameter | Deskripsi                            |
| --------- | ------------------------------------ |
| template  | path view relatif dari `app/Modules` |
| data      | data yang dikirim ke template        |
| layout    | apakah menggunakan layout global     |

Contoh:

```php
return $this->view(
    'Blog/Views/post.view.php',
    [
        'title' => 'My Post'
    ]
);
```

Tanpa layout:

```php
return $this->view(
    'Blog/Views/card.view.php',
    $data,
    false
);
```

---

# Asset Helper

Controller otomatis menyisipkan object `$asset` ke dalam view.

Di dalam template dapat langsung digunakan:

```php
<link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
<script src="<?= $asset->url('js/app.js') ?>"></script>
```

---

# Mengakses Input Request

Controller menyediakan helper:

```
input(string $key, mixed $default = null)
```

Contoh:

```php
$name = $this->input('name');
```

Ini membaca input dari:

* GET
* POST
* JSON body

tergantung implementasi `Request`.

---

# Mengakses Config

Config dapat diakses langsung melalui controller.

```
config(string $key, mixed $default = null)
```

Contoh:

```php
$debug = $this->config('app.debug');
```

---

# Menggunakan Cache

Controller menyediakan akses cache melalui helper:

```php
$cache = $this->cache();
```

Contoh penggunaan:

```php
$posts = $this->cache()->remember('home.posts', function () {
    return (new PostModel())->getAll();
});
```

Cache driver mengikuti konfigurasi `.env`.

---

# JSON Response

Untuk endpoint API gunakan:

```php
$this->json([
    'success' => true
]);
```

Contoh controller API:

```php
public function api(): void
{
    $this->json([
        'success' => true,
        'message' => 'Welcome to Caracal API'
    ]);
}
```

Method ini akan:

* mengatur HTTP status code
* mengirim header `Content-Type: application/json`
* mengencode data ke JSON

---

# Redirect

Untuk redirect halaman gunakan:

```php
$this->redirect('/dashboard');
```

Method ini akan mengirim header:

```
Location: /dashboard
```

dan menghentikan eksekusi script.

---

# Ringkasan Method

| Method     | Fungsi                            |
| ---------- | --------------------------------- |
| view()     | render template HTML              |
| json()     | response JSON API                 |
| redirect() | redirect HTTP                     |
| input()    | mengambil input request           |
| config()   | membaca konfigurasi               |
| cache()    | mengakses cache instance aplikasi |