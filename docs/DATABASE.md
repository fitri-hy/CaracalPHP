# 📘 CaracalPHP – Database Documentation

Class:

```php
Caracal\Core\Database
```

Database menggunakan:

```
Illuminate\Database\Capsule\Manager
```

Artinya CaracalPHP memakai **Eloquent ORM (Illuminate Database)** sebagai engine database.

---

# 🎯 Tujuan Class Database

Class ini berfungsi untuk:

* Inisialisasi koneksi database
* Konfigurasi MySQL atau SQLite
* Boot Eloquent ORM
* Menyediakan akses global ke Capsule

---

# 1️⃣ Konfigurasi Database

Database membaca konfigurasi dari:

```php
$config->get('db')
```

Contoh struktur `config/config.php`:

```php
return [
    'db' => [
        'enabled'   => true,
        'driver'    => 'mysql', // mysql atau sqlite
        'host'      => '127.0.0.1',
        'port'      => 3306,
        'name'      => 'caracal',
        'user'      => 'root',
        'pass'      => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]
];
```

---

# ⚙ 2️⃣ Cara Kerja Constructor

```php
public function __construct(Config $config)
```

Langkah yang dilakukan:

1. Ambil config `db`
2. Jika `enabled` kosong → tidak konek
3. Buat instance Capsule
4. Tambahkan koneksi
5. Set global
6. Boot Eloquent
7. Tandai `$connected = true`

Jika gagal koneksi:

```
RuntimeException: Database connection failed
```

---

# 3️⃣ Driver yang Didukung

## ✅ MySQL (default)

Jika driver bukan `sqlite`, maka akan menggunakan MySQL.

## ✅ SQLite

Jika:

```php
'driver' => 'sqlite'
```

Maka:

* Database file otomatis dibuat di:

```
/database/database.sqlite
```

Jika file belum ada, otomatis dibuat dengan `touch()`.

---

# 4️⃣ Mengecek Apakah Database Aktif

```php
$db->isConnected();
```

Return:

```
true / false
```

---

# 5️⃣ Mengakses Capsule Instance

```php
$db->capsule();
```

⚠ Akan melempar exception jika database tidak terkoneksi.

---

# 6️⃣ Cara Akses Global (Direkomendasikan)

Gunakan:

```php
Database::connection();
```

Implementasi:

```php
public static function connection(): Capsule
```

Method ini:

1. Ambil instance Application
2. Pastikan database aktif
3. Return Capsule

Jika database disabled:

```
RuntimeException: Database disabled. Set DB_ENABLED=true
```

---

# 7️⃣ Contoh Penggunaan Query Builder

```php
use Caracal\Core\Database;

$capsule = Database::connection();

$users = $capsule->table('users')->get();
```

---

# 8️⃣ Contoh Insert Data

```php
Database::connection()
    ->table('users')
    ->insert([
        'name' => 'John',
        'email' => 'john@example.com'
    ]);
```

---

# 9️⃣ Contoh Menggunakan Eloquent Model

Karena `bootEloquent()` dipanggil, kamu bisa langsung buat model:

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
}
```

Lalu gunakan:

```php
$users = User::all();
```

---

# 🔐 Error Handling

Jika koneksi gagal:

```
RuntimeException("Database connection failed: ...")
```

Jika akses connection saat disabled:

```
RuntimeException("Database disabled. Set DB_ENABLED=true")
```

---

# 📌 Ringkasan Method

| Method        | Fungsi                 |
| ------------- | ---------------------- |
| __construct() | Inisialisasi koneksi   |
| isConnected() | Cek status koneksi     |
| capsule()     | Ambil instance Capsule |
| connection()  | Akses global Capsule   |