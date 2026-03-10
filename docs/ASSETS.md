# CaracalPHP – Asset Documentation

Class

```php
Caracal\Core\Asset
```

`Asset` is used to generate public asset URLs such as CSS, JavaScript, and images located in the following directory.

```
public/assets/
```

---

## Asset Structure

Example directory structure

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

All files in this directory can be accessed through the `Asset` class.

---

## Basic Configuration

Asset URLs are generated using the environment configuration.

```
APP_URL=http://localhost
```

Example result

```
http://localhost/assets/css/styles.css
```

---

## Creating an Asset Instance

In a controller

```php
use Caracal\Core\Asset;

$asset = new Asset();
```

---

## Generating an Asset URL

Main method

```php
$asset->url('css/styles.css');
```

Output

```
http://localhost/assets/css/styles.css
```

---

## Using Assets in a Controller

Example controller

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

## Using Assets in a View

```php
<link rel="stylesheet" href="<?= $css ?>">
<script src="<?= $js ?>"></script>
```

---

## Direct Asset Usage in a View

If the `$asset` instance is available in the view

```php
<link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
```

---

## Cache Busting (Versioning)

To avoid browser caching issues

```php
$asset->version('css/styles.css');
```

Output

```
/assets/css/styles.css?v=1700001111
```

The `v` parameter is generated from the file modification time.

---

## CSS Helper

```php
<?= $asset->css('css/styles.css') ?>
```

Output

```html
<link rel="stylesheet" href="/assets/css/styles.css?v=1700001111">
```

---

## JavaScript Helper

```php
<?= $asset->js('js/app.js') ?>
```

Output

```html
<script src="/assets/js/app.js?v=1700001111"></script>
```

---

## Image Helper

```php
<?= $asset->image('images/logo.png','Logo') ?>
```

Output

```html
<img src="/assets/images/logo.png?v=1700001111" alt="Logo">
```

---

## Checking if a File Exists

```php
$asset->exists('css/styles.css');
```

Return value

```
true / false
```

---

## Getting the Physical Asset Path

```php
$path = $asset->path('css/styles.css');
```

Output

```
/project/public/assets/css/styles.css
```