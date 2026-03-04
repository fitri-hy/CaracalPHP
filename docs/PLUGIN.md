# 📘 CaracalPHP – Plugin System Documentation

Class:

```php
Caracal\Core\Plugin
```

`Plugin` adalah sistem extensibility utama di CaracalPHP.

Class ini bertanggung jawab untuk:

* Load file plugin
* Menjalankan lifecycle plugin
* Mengelola service container sederhana
* Menyediakan event hook system

---

# 🎯 Tujuan Plugin System

* Membuat framework extensible
* Mendukung service registration
* Mendukung singleton service
* Mendukung event-driven hooks
* Mendukung priority execution

---

# 1️⃣ Struktur Internal

```php
protected array $plugins = [];
protected array $services = [];
protected array $instances = [];
protected array $hooks = [];
```

Penjelasan:

| Property   | Fungsi                    |
| ---------- | ------------------------- |
| $plugins   | Menyimpan definisi plugin |
| $services  | Registry service          |
| $instances | Instance singleton        |
| $hooks     | Event hooks               |

---

# 2️⃣ Format File Plugin

File plugin harus:

* Berupa file PHP
* Return array
* Minimal return array kosong

Contoh:

```php
return [
    'priority' => 10,

    'register' => function($plugin) {
        // register service
    },

    'boot' => function($plugin) {
        // boot logic
    },

    'callback' => function($plugin) {
        // optional callback
    }
];
```

---

# 3️⃣ Method load()

```php
public function load(string $path): void
```

Fungsi:

* Include file plugin
* Validasi file ada
* Validasi return berupa array
* Simpan ke $plugins

Jika file tidak ada:

```php
throw new \Exception("Plugin file {$path} not found");
```

Jika tidak return array:

```php
throw new \Exception("Plugin file must return an array");
```

---

# 4️⃣ Method run()

```php
public function run(): void
```

Menjalankan lifecycle plugin.

---

## Step 1 – Urutkan Berdasarkan Priority

```php
usort(...);
```

Semakin besar priority → dijalankan lebih dulu.

Default priority = 0.

---

## Step 2 – Jalankan register()

Semua plugin dipanggil bagian register dulu.

```php
$plugin['register']($this);
```

Digunakan untuk:

* Register service
* Register singleton
* Register hook

---

## Step 3 – Jalankan boot() dan callback()

Setelah semua register selesai:

* boot()
* callback()

Dieksekusi sesuai urutan priority.

---

# 5️⃣ Service Container

Plugin berfungsi sebagai service container ringan.

---

## set()

```php
public function set(string $name, $service): void
```

Menyimpan service biasa.

Contoh:

```php
$plugin->set('logger', new Logger());
```

---

## singleton()

```php
public function singleton(string $name, callable $factory): void
```

Menyimpan factory dan instance akan dibuat sekali.

Contoh:

```php
$plugin->singleton('mailer', function($plugin) {
    return new Mailer();
});
```

---

## get()

```php
public function get(string $name)
```

Mengambil service.

Perilaku:

1. Jika tidak ada → return null
2. Jika singleton → buat instance sekali
3. Jika callable → jalankan dan return
4. Jika object biasa → return langsung

---

# 6️⃣ Event Hook System

Plugin memiliki sistem event internal.

---

## on()

```php
public function on(string $event, callable $callback): void
```

Register event listener.

Contoh:

```php
$plugin->on('before_request', function() {
    echo "Request started";
});
```

---

## trigger()

```php
public function trigger(string $event, ...$params): void
```

Menjalankan semua callback event tersebut.

Contoh:

```php
$plugin->trigger('before_request');
```

---

# 📌 Integrasi dengan Kernel

Kernel memanggil event berikut:

* before_request
* before_dispatch
* after_dispatch
* response_ready
* on_error
* after_response

Artinya plugin bisa hook ke seluruh lifecycle request.

---

# 7️⃣ Method all()

```php
public function all(): array
```

Mengembalikan seluruh plugin yang sudah diload.

---

# 📌 Contoh Plugin Lengkap

```php
return [

    'priority' => 100,

    'register' => function($plugin) {

        $plugin->singleton('logger', function() {
            return new \Caracal\Core\Logger();
        });

        $plugin->on('before_request', function() {
            echo "Plugin active";
        });
    },

    'boot' => function($plugin) {
        // boot logic
    }
];
```