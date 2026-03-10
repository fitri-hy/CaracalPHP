# CaracalPHP – Asset Usage Guide

Class:

```php
Caracal\Core\Asset
```

`Asset` digunakan untuk menghasilkan URL asset publik seperti CSS, JavaScript, dan gambar yang berada di dalam folder:

```
public/assets/
```

---

## Struktur Asset

Contoh struktur folder:

```
public/
 └ assets/
    ├ css/
    │  └ styles.css
    ├ js/
    │  └ app.js
    └ images/
       └ logo.png
```

Semua file dalam folder ini dapat diakses melalui class `Asset`.

---

## Konfigurasi Dasar

URL asset dibangun dari konfigurasi environment:

```
APP_URL=http://localhost
```

Contoh hasil:

```
http://localhost/assets/css/styles.css
```

---

## Membuat Instance Asset

Di controller:

```php
use Caracal\Core\Asset;

$asset = new Asset();
```

---

## Menghasilkan URL Asset

Method utama:

```php
$asset->url('css/styles.css');
```

Output:

```
http://localhost/assets/css/styles.css
```

---

## Menggunakan di Controller

Contoh controller:

```php
class ExampleController extends Controller
{
    public function index()
    {
        $asset = new Asset();

        return $this->render('example.view', [
            'css' => $asset->url('css/styles.css'),
            'js'  => $asset->url('js/app.js')
        ]);
    }
}
```

---

## Menggunakan di View

```php
<link rel="stylesheet" href="<?= $css ?>">
<script src="<?= $js ?>"></script>
```

---

## Menggunakan Asset Langsung di View

Jika `$asset` tersedia di view:

```php
<link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
```

---

## Cache Busting (Versioning)

Untuk menghindari cache browser, gunakan:

```php
$asset->version('css/styles.css');
```

Output:

```
/assets/css/styles.css?v=1700001111
```

Parameter `v` berasal dari waktu modifikasi file.

---

## Helper CSS

```php
<?= $asset->css('css/styles.css') ?>
```

Output:

```html
<link rel="stylesheet" href="/assets/css/styles.css?v=1700001111">
```

---

## Helper JavaScript

```php
<?= $asset->js('js/app.js') ?>
```

Output:

```html
<script src="/assets/js/app.js?v=1700001111"></script>
```

---

## Helper Image

```php
<?= $asset->image('images/logo.png','Logo') ?>
```

Output:

```html
<img src="/assets/images/logo.png?v=1700001111" alt="Logo">
```

---

## Mengecek Apakah File Ada

```php
$asset->exists('css/styles.css');
```

Return:

```
true / false
```

---

## Mendapatkan Path Fisik Asset

```php
$path = $asset->path('css/styles.css');
```

Output:

```
/project/public/assets/css/styles.css
```

---

## Best Practice

Disarankan:

* Simpan semua asset di `public/assets`
* Gunakan helper `css()` dan `js()` untuk mempermudah view
* Gunakan `version()` untuk menghindari cache browser

Hindari:

* Hardcode URL `/assets/...`
* Menyimpan asset di luar folder `public`