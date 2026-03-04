# 📘 CaracalPHP – View Documentation

## Overview

`Caracal\Core\View` adalah **template rendering engine** untuk CaracalPHP yang mendukung:

* Render **module views** dengan data dinamis
* Menggunakan **layout global** secara otomatis
* **Caching** hasil render untuk meningkatkan performa
* Exception handling jika file view tidak ditemukan

> 💡 Semua view menggunakan **PHP native templates**, sehingga mudah di-extend dan fleksibel.

---

## Initialization

```php
use Caracal\Core\View;

$view = new View();
```

Tidak perlu konfigurasi tambahan. Secara default:

* Base path: `app/Modules`
* Layout global: `app/Modules/layout.view.php`

---

## Basic Usage

### Render a View

```php
echo $view->render('Home/Views/home.view.php', [
    'title' => 'Welcome',
    'name'  => 'John Doe'
]);
```

* Parameter pertama: path ke file view relatif dari `app/Modules` atau full path
* Parameter kedua: array data yang akan diextract ke template
* Parameter ketiga (opsional): `useLayout` (default `true`), apakah render menggunakan layout global

---

### Render Without Layout

```php
echo $view->render('Home/Views/partial.view.php', ['item' => $item], false);
```

> Gunakan `false` jika ingin render **partial view** atau snippet saja.

---

## Layout Global

File layout default: `app/Modules/layout.view.php`

Contoh isi layout:

```php html
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'CaracalPHP' ?></title>
</head>
<body>
    {{content}}
</body>
</html>
```

* Placeholder `{{content}}` akan digantikan dengan hasil render view
* Layout bisa di-cache untuk performa lebih baik (`storage/cache`)

---

## Passing Data to Views

```php
$data = [
    'username' => 'John Doe',
    'messages' => ['Welcome!', 'You have 3 notifications']
];

echo $view->render('Home/Views/dashboard.view.php', $data);
```

Di dalam view:

```php
<h1>Hello, <?= $username ?></h1>
<ul>
<?php foreach ($messages as $msg): ?>
    <li><?= $msg ?></li>
<?php endforeach; ?>
</ul>
```

> Semua variabel dari `$data` diextract secara otomatis ke dalam scope view.

---

## Caching

* View hasil render **di-cache** menggunakan core `Cache` untuk mempercepat load
* Cache key dibuat berdasarkan **hash template + data**
* Layout global juga di-cache (`layout_global`)
* Tidak perlu setup manual, cache otomatis digunakan

---

## Error Handling

Jika view file tidak ditemukan:

```php
throw new \Exception("View {$file} not found");
```

> Pastikan path template sesuai dengan `app/Modules/<Module>/Views/<view>.php`.

---

## Advanced Usage: Multiple Layouts

Anda bisa membuat layout berbeda dengan cara:

```php
$customLayout = __DIR__ . '/CustomLayout/layout.php';
echo $view->render('Home/Views/home.view.php', ['title'=>'Dashboard', 'content'=>$content], true);
```

> Cukup include layout custom di parameter render, atau override `$view->layout` jika dibutuhkan.

---

## Notes

* View engine **tidak tergantung framework eksternal**, murni PHP
* Mendukung **template inheritance sederhana** via `{{content}}`
* Data otomatis diextract → variabel tersedia langsung di template
* Cocok digunakan untuk **module-based structure** CaracalPHP