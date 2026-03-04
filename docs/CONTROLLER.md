# 📘 CaracalPHP – Base Controller Documentation

Class dasar controller:

```php
Caracal\Core\Controller
```

Semua controller module sebaiknya mewarisi class ini.

---

# 🎯 Tujuan Base Controller

Class ini menyediakan secara otomatis:

* `$request` → instance `Request`
* `$view` → instance `View`
* `$asset` → instance `Asset`
* Method `render()` untuk merender template

---

# 📦 Properti yang Tersedia

Saat controller di-extend, properti berikut otomatis tersedia:

```php
protected Request $request;
protected View $view;
protected Asset $asset;
```

Diinisialisasi dalam constructor:

```php
$this->request = Request::capture();
$this->view    = new View();
$this->asset   = new Asset();
```

Artinya setiap controller module langsung memiliki akses ke:

* HTTP request aktif
* View engine
* Asset helper

---

# 🧩 1️⃣ Cara Menggunakan Controller di Module

Lokasi:

```
app/Modules/{ModuleName}/Controllers/
```

Contoh:

```php
<?php

namespace App\Modules\Home\Controllers;

use Caracal\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('home.view', [
            'title' => 'Homepage'
        ]);
    }
}
```

---

# 🖼 2️⃣ Cara Kerja `render()`

Method:

```php
protected function render(string $tpl, array $data = []): string
```

Yang dilakukan:

1. Menambahkan `$asset` ke dalam `$data`
2. Memanggil `$this->view->render()`
3. Mengembalikan hasil render sebagai string

Implementasi:

```php
$data['asset'] = $this->asset;
return $this->view->render($tpl, $data);
```

---

# 🖼 3️⃣ Akses Asset di View

Karena `render()` otomatis menyisipkan:

```php
$data['asset'] = $this->asset;
```

Maka di dalam view kamu bisa langsung:

```php
<link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
<script src="<?= $asset->url('js/app.js') ?>"></script>
```

Tanpa perlu mengirim `$asset` manual dari controller.

---

# 🌐 4️⃣ Menggunakan Request di Controller

Karena `$request` sudah tersedia:

```php
public function store()
{
    $name = $this->request->input('name');

    // proses data
}
```

`Request::capture()` sudah otomatis dipanggil di constructor.

---

# 🎯 5️⃣ Menggunakan View Secara Langsung

Jika diperlukan:

```php
$html = $this->view->render('home.view', [
    'title' => 'Example'
]);
```

Namun disarankan tetap menggunakan:

```php
$this->render(...)
```

Karena `render()` sudah menyisipkan `$asset`.

---

# 📌 Ringkasan Fitur Base Controller

| Fitur      | Deskripsi                      |
| ---------- | ------------------------------ |
| `$request` | HTTP request aktif             |
| `$view`    | View renderer                  |
| `$asset`   | Asset URL generator            |
| `render()` | Render template + inject asset |