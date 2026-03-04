# 📘 CaracalPHP Assets Usage Guide

Dokumentasi ini menjelaskan cara menggunakan:

```php
Caracal\Core\Asset
```

untuk menghasilkan URL asset (CSS, JS, image) yang berada di folder:

```id="public-assets"
public/assets/
```

Contoh struktur:

```
public/
 └── assets/
     ├── css/styles.css
     ├── js/app.js
     └── images/logo.png
```

---

# ⚙️ Konfigurasi Dasar

Asset URL dibangun berdasarkan nilai:

```env
APP_URL=http://localhost
```

Class `Asset` akan menghasilkan URL seperti:

```
http://localhost/assets/css/styles.css
```

---

# 🧩 1. Penggunaan di Controller

Lokasi:

```id="controller-path"
app/Modules/{ModuleName}/Controllers/
```

Contoh:

```php
<?php

namespace App\Modules\Example\Controllers;

use Caracal\Core\Controller;
use Caracal\Core\Asset;

class ExampleController extends Controller
{
    public function index()
    {
        $asset = new Asset();

        $css = $asset->url('css/styles.css');
        $js  = $asset->url('js/app.js');

        return $this->view('example.view', [
            'css' => $css,
            'js'  => $js
        ]);
    }
}
```

### Hasil

Controller mengirimkan URL asset ke view sebagai variabel:

* `$css`
* `$js`

---

# 🖼 2. Penggunaan di View

Lokasi:

```id="view-path"
app/Modules/*/Views/*.view.php
```

Contoh:

```php
<link rel="stylesheet" href="<?= $css ?>">
<script src="<?= $js ?>"></script>
```

---

# 🧱 3. Memanggil Asset Langsung di View

Jika object `$asset` tersedia di view, Anda bisa memanggilnya langsung:

```php
<?= $asset->url('css/styles.css') ?>
```

Contoh lengkap:

```php
<link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
<script src="<?= $asset->url('js/app.js') ?>"></script>
<img src="<?= $asset->url('images/logo.png') ?>">
```

📌 Pastikan object `$asset` dikirim dari controller:

```php
return $this->view('example.view', [
    'asset' => new Asset()
]);
```

---

# 🌐 4. Contoh Output URL

Jika:

```env
APP_URL=http://localhost:8000
```

Maka:

```php
$asset->url('css/styles.css');
```

Akan menghasilkan:

```
http://localhost:8000/assets/css/styles.css
```

---

# 📁 5. Lokasi File Asset

Semua asset publik harus berada di:

```id="asset-location"
public/assets/
```

Contoh pemanggilan:

| File Fisik                    | Pemanggilan       |
| ----------------------------- | ----------------- |
| public/assets/css/styles.css  | `css/styles.css`  |
| public/assets/js/app.js       | `js/app.js`       |
| public/assets/images/logo.png | `images/logo.png` |

---

# 🧠 Best Practice

### Disarankan:

* Kirim URL dari controller ke view
* Gunakan satu instance `Asset`
* Simpan asset hanya di `public/assets`

### Hindari:

* Hardcode URL seperti `/assets/...`
* Menaruh asset di luar folder `public`

---

# 🏁 Ringkasan

| Lokasi              | Cara Penggunaan                        |
| ------------------- | -------------------------------------- |
| Controller          | `$asset = new Asset();`                |
| Generate URL        | `$asset->url('css/styles.css')`        |
| View (via variable) | `<?= $css ?>`                          |
| View (direct call)  | `<?= $asset->url('css/styles.css') ?>` |
