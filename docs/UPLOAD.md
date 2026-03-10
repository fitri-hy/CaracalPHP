# CaracalPHP – Upload Documentation

Class:

```php id="u7q2xp"
Caracal\Core\Upload
```

`Upload` is a **professional file upload engine** for CaracalPHP. It supports:

* Single and multiple file uploads
* File size validation (`UPLOAD_MAX_SIZE` from `.env` or custom)
* File extension and MIME type validation
* Automatic file renaming to avoid collisions
* Optional overwrite of existing files
* Storing files in **internal storage** (`Storage`)
* Returning full metadata after upload
* Complete error handling

> Default storage folder: `storage/uploads`, customizable via configuration.

---

## Initialization

```php id="r8y1nw"
use Caracal\Core\Upload;

// Default initialization
$upload = new Upload();

// Custom configuration
$upload = new Upload([
    'path' => __DIR__.'/../storage/private_files',
    'max_size' => 2 * 1024 * 1024, // 2 MB
    'allowed_ext' => ['jpg','png','gif'],
    'allowed_mime' => ['image/jpeg','image/png','image/gif'],
]);
```

> If not set, `max_size` defaults to `.env` `UPLOAD_MAX_SIZE` (default 5M).

---

## Single File Upload

```php id="m4t3kb"
$file = $_FILES['avatar'];

$result = $upload->save($file, 'users/avatars');

// Example result metadata
print_r($result);
/*
[
    [
        'original_name' => 'photo.jpg',
        'stored_name'   => 'photo.jpg',
        'path'          => 'users/avatars/photo.jpg',
        'size'          => 102400,
        'extension'     => 'jpg'
    ]
]
*/
```

---

## Multiple File Upload

```php id="n9p5vx"
$files = $_FILES['documents'];

$results = $upload->save($files, 'users/docs');

foreach ($results as $file) {
    echo "Uploaded: {$file['stored_name']} ({$file['size']} bytes)\n";
}
```

---

## Allowed File Types and Extensions

```php id="t2v8qr"
$upload = new Upload([
    'allowed_ext' => ['jpg','png','pdf'],
    'allowed_mime' => ['image/jpeg','image/png','application/pdf']
]);

$upload->save($_FILES['file'], 'uploads'); // validates file type
```

> Invalid files throw an **Exception** with a clear message.

---

## Overwrite File

```php id="w3m1yz"
$upload->save($_FILES['avatar'], 'users/avatars', true); // overwrite existing file
```

* Default: **no overwrite** → automatically renames if file exists
* Pass `true` to overwrite existing file

---

## Automatic Rename

If a file exists and overwrite is `false`, the name will automatically change:

```
photo.jpg → photo_1.jpg → photo_2.jpg ...
```

---

## Max Upload Size

* Can be set via configuration (`max_size` in bytes)
* Defaults from `.env`:

```dotenv
UPLOAD_MAX_SIZE=5M
```

* Automatically converted: `5M` → 5 * 1024 * 1024 bytes

---

## Error Handling

`Upload::save()` throws **Exceptions** for:

* File too large
* Disallowed extension
* Disallowed MIME type
* Partial upload or failed write
* Target folder missing

---

## Full Example

```php id="v7r2ts"
$upload = new \Caracal\Core\Upload([
    'allowed_ext' => ['jpg','png','gif'],
    'allowed_mime' => ['image/jpeg','image/png','image/gif'],
]);

try {
    $result = $upload->save($_FILES['avatar'], 'users/avatars');
    echo "File uploaded: " . $result[0]['path'];
} catch (\Exception $e) {
    echo "Upload failed: " . $e->getMessage();
}
```

---

## Notes

* Default storage path: `storage/uploads`
* Custom path allowed for **module/plugin internal storage**
* Supports single and multiple uploads
* Returns full metadata for each file
* Validates file size, extension, and MIME type
* Auto-renames files to prevent conflicts
* Exception handling for all upload errors
