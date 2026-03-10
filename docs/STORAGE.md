# CaracalPHP – Storage Documentation

Class:

```php
Caracal\Core\Storage
```

`Storage` is a **private file storage abstraction** for CaracalPHP.
It is designed to store **internal module files**, **uploads**, or content that is not publicly accessible.

Key features:

* Read and write files with managed paths
* Check file existence
* Delete files
* Automatically create directories
* Support custom base paths
* Support **storage drivers** from `.env` (currently `local`)
* Limit **upload size** according to `UPLOAD_MAX_SIZE`

Defaults:

* Base path: `storage/uploads`
* Driver: `local`
* Upload max size: `5M`

---

## Initialization

### Default Storage Path

```php
use Caracal\Core\Storage;

$storage = new Storage();
echo $storage->path('example.txt'); 
// C:\path_to_project\storage\uploads/example.txt
```

### Custom Storage Path

```php
$storage = new Storage(__DIR__.'/../storage/private_files');
```

> Storage will automatically create the directory if it does not exist.

---

## Storage Driver & Max Upload

Driver and upload size are read from `.env`:

```env
FILESYSTEM_DRIVER=local
UPLOAD_MAX_SIZE=5M
```

```php
$storage = new Storage();
```

* Uses **local driver** by default
* Files exceeding `UPLOAD_MAX_SIZE` will **throw an exception** when using `put()`

---

## Basic File Operations

### Write File (`put`)

```php
$storage->put('docs/readme.txt', 'Hello CaracalPHP!');
```

* Creates `readme.txt` in `storage/uploads/docs/`
* Automatically creates directories if missing
* Checks file size according to `UPLOAD_MAX_SIZE`
* Returns `true` if successful

---

### Read File (`get`)

```php
$content = $storage->get('docs/readme.txt');
echo $content; // Hello CaracalPHP!
```

* Returns `null` if the file does not exist

---

### Delete File (`delete`)

```php
$storage->delete('docs/readme.txt');
```

* Deletes the file
* Returns `true` if file existed and was deleted
* Returns `false` if file did not exist

---

### Check File Existence (`exists`)

```php
if ($storage->exists('docs/readme.txt')) {
    echo "File exists!";
}
```

---

### Get Full Path (`path`)

```php
$fullPath = $storage->path('docs/readme.txt');
echo $fullPath; 
// C:\path_to_project\storage\uploads/docs/readme.txt
```

---

### Create Directory (`makeDir`)

```php
$storage->makeDir('images/users');
```

* Creates a new directory under the **base path**
* Returns `true` if successful or already exists

---

## Example: Module File Storage

```php
$storage = new \Caracal\Core\Storage();

// Save user avatar
$storage->put('users/123/avatar.png', $imageContent);

// Check if file exists
if ($storage->exists('users/123/avatar.png')) {
    echo "Avatar ready for backend access!";
}

// Retrieve file content
$content = $storage->get('users/123/avatar.png');

// Delete file
$storage->delete('users/123/avatar.png');
```

---

## Notes

* All operations are **private**; not for public access (use `public/uploads` for user-accessible uploads)
* Paths are managed **relative to the base path**
* Missing directories are automatically created during `put()` or `makeDir()`
* Can be used by **modules** or **plugins** for internal storage
* **Driver** and **upload max size** are configured via `.env` (`FILESYSTEM_DRIVER` & `UPLOAD_MAX_SIZE`)
* Other drivers are rejected if not implemented
