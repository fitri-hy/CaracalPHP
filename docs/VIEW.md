# CaracalPHP – View Documentation

`Caracal\Core\View` adalah **template rendering engine bawaan CaracalPHP** untuk menampilkan halaman HTML menggunakan **PHP native templates**.

View engine ini dirancang agar:

* ringan
* fleksibel
* aman
* cepat melalui caching

Fitur utama:

* render view berbasis **module**
* dukungan **global layout**
* **view caching**
* **layout caching**
* **partial view include**
* **custom layout**
* **path security protection**

---

# Struktur View

Semua view berada dalam folder:

```
app/Modules
```

Contoh struktur module:

```
app
 └─ Modules
     └─ Welcome
         └─ Views
             └─ welcome.view.php
```

Layout global berada di:

```
app/Modules/layout.view.php
```

---

# Initialization

View biasanya digunakan dari Controller.

Contoh penggunaan manual:

```php
use Caracal\Core\View;

$view = new View();
```

Biasanya dipanggil melalui Controller:

```php
$this->render('Welcome/Views/welcome.view.php');
```

---

# Render View

Method utama:

```php
render(string $template, array $data = [], bool $useLayout = true): string
```

Parameter:

| Parameter | Deskripsi                            |
| --------- | ------------------------------------ |
| template  | path view relatif dari `app/Modules` |
| data      | data yang dikirim ke template        |
| useLayout | apakah menggunakan layout            |

Contoh:

```php
echo $view->render(
    'Welcome/Views/welcome.view.php',
    [
        'title' => 'Welcome',
        'message' => 'Hello Caracal'
    ]
);
```

---

# Data di Dalam View

Data yang dikirim dari controller otomatis menjadi variabel di template.

Controller:

```php
$view->render('Welcome/Views/dashboard.view.php', [
    'username' => 'John',
    'notifications' => 5
]);
```

Di view:

```php
<h1>Hello <?= $username ?></h1>
<p>You have <?= $notifications ?> notifications</p>
```

View engine menggunakan:

```
extract($data)
```

untuk membuat variabel di dalam template.

---

# Layout System

Secara default semua view menggunakan **layout global**.

Lokasi:

```
app/Modules/layout.view.php
```

Contoh layout:

```php
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Caracal' ?></title>
</head>
<body>

{{content}}

</body>
</html>
```

Placeholder:

```
{{content}}
```

akan digantikan oleh hasil render view.

---

# Render Tanpa Layout

Jika ingin merender view tanpa layout:

```php
$view->render(
    'Welcome/Views/card.view.php',
    ['title' => 'Product'],
    false
);
```

Parameter `false` akan menonaktifkan layout.

Biasanya digunakan untuk:

* partial template
* ajax response
* widget HTML

---

# Custom Layout

Layout dapat diganti secara runtime.

Contoh:

```php
$view->setLayout(
    APP_PATH . '/Modules/Admin/layout.view.php'
);
```

Mengambil layout aktif:

```php
$layout = $view->getLayout();
```

---

# Partial View

View dapat memanggil view lain menggunakan method `partial()`.

Contoh di dalam view:

```php
<?php $this->partial('Welcome/Views/header.view.php'); ?>
```

Dengan data:

```php
<?php $this->partial(
    'Blog/Views/post.view.php',
    ['post' => $post]
); ?>
```

Partial **tidak menggunakan layout** dan langsung dirender.

---

# View Caching

Caracal memiliki **built-in caching untuk view**.

Caching aktif jika:

```
CACHE_ENABLED=true
```

di file `.env`.

Caching dilakukan untuk:

* hasil render view
* template layout

Cache key view:

```
view_{hash}
```

Hash dibuat dari:

```
md5(template + data_keys)
```

Contoh:

```
view_a3f5e8b9c7d2
```

Layout cache:

```
layout_template
```

Layout hanya di-cache sebagai **template HTML**, bukan hasil render, sehingga aman untuk digunakan oleh banyak view.

Manfaat caching:

* mempercepat rendering halaman
* mengurangi parsing template
* mengurangi IO file

---

# Cara Kerja Rendering

Urutan proses rendering:

1. cek apakah cache aktif (`cache.enabled`)
2. cek cache view
3. render file view
4. load layout template
5. ganti `{{content}}` dengan hasil view
6. simpan hasil render ke cache
7. return HTML

---

# Security

View engine memiliki proteksi terhadap **directory traversal attack**.

Path view divalidasi menggunakan:

```
realpath()
```

dan dipastikan tetap berada dalam folder:

```
app/Modules
```

Sehingga path seperti:

```
../../../.env
```

tidak dapat diakses oleh sistem view.

---

# Error Handling

Jika file view tidak ditemukan:

```
Exception: View {file} not found
```

Jika path view tidak valid:

```
Exception: Invalid view path
```

Pastikan path template mengikuti struktur:

```
Module/Views/view.view.php
```

Contoh:

```
Welcome/Views/welcome.view.php
```

---

# Best Practice

Gunakan view untuk:

* halaman HTML
* template UI
* komponen tampilan

Hindari:

* query database di view
* logika bisnis kompleks
* manipulasi data besar

Controller bertanggung jawab menyiapkan data sebelum dikirim ke view.

---

# Contoh Penggunaan di Controller

```php
public function index(): string
{
    return $this->render(
        'Welcome/Views/welcome.view.php',
        [
            'title' => 'Welcome',
            'message' => 'Welcome to Caracal Framework'
        ]
    );
}
```

---

# Summary

Kemampuan View Engine Caracal:

| Feature             | Supported |
| ------------------- | --------- |
| Module Views        | yes       |
| Global Layout       | yes       |
| Custom Layout       | yes       |
| Partial View        | yes       |
| View Cache          | yes       |
| Layout Cache        | yes       |
| Path Security       | yes       |
| Native PHP Template | yes       |