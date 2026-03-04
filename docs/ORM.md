# 📘 CaracalPHP – ORM Documentation

Class:

```php id="7m3d2k"
Caracal\Core\ORM
```

`ORM` adalah abstract base model yang meng-extend Eloquent Model dari Illuminate Database.

Class ini berfungsi sebagai:

* Bridge antara CaracalPHP dan Eloquent ORM
* Validator bahwa database aktif
* Wrapper sederhana untuk query builder

---

# 🎯 Tujuan ORM

* Menggunakan Eloquent tanpa full Laravel
* Menjamin database aktif sebelum model digunakan
* Menyediakan akses query melalui static method

---

# 1️⃣ Inheritance

```php id="5s0hty"
abstract class ORM extends Model
```

Artinya semua model di CaracalPHP harus:

```php id="k0m6x2"
class User extends ORM
```

Bukan langsung extend `Model`.

---

# 2️⃣ Protected Property

```php id="yt3b8p"
protected $guarded = [];
```

Artinya:

* Semua field boleh di-mass assign
* Tidak ada proteksi field bawaan

---

# 3️⃣ Constructor

```php id="p3q0cm"
public function __construct(array $attributes = [])
```

Sebelum parent constructor dipanggil, dilakukan dua validasi penting.

---

## Validasi 1 – Database Aktif

```php id="n1z6ov"
$db = Application::getInstance()->db();

if (!$db || !$db->isConnected()) {
    throw new RuntimeException("ORM is used but the database is not active.");
}
```

Artinya:

* Jika database disabled
* Atau koneksi gagal
* Maka ORM tidak bisa digunakan

---

## Validasi 2 – Capsule Tersedia

```php id="3m6vyo"
if (!class_exists(Capsule::class)) {
    throw new RuntimeException("Illuminate Database Capsule is not yet available.");
}
```

Artinya:

* Illuminate Database harus terinstall
* Capsule harus tersedia

---

# 4️⃣ Method table()

```php id="ov7zwr"
public static function table(): \Illuminate\Database\Query\Builder
```

Return:

```php id="m3dp9i"
static::query();
```

Digunakan untuk mendapatkan query builder.

---

# 📌 Contoh Model

```php id="5nldv7"
use Caracal\Core\ORM;

class User extends ORM
{
    protected $table = 'users';
}
```

---

# 📌 Contoh Penggunaan

## Mengambil Semua Data

```php id="pltn1o"
$users = User::all();
```

---

## Query Builder via table()

```php id="a8e5kz"
$activeUsers = User::table()
    ->where('status', 'active')
    ->get();
```

---

## Create Data

```php id="rgm3lm"
User::create([
    'name' => 'John',
    'email' => 'john@example.com'
]);
```

---

## Update Data

```php id="2jndsy"
$user = User::find(1);
$user->update(['name' => 'Updated']);
```

---

## Delete Data

```php id="lj4i6b"
User::destroy(1);
```

---

# 📌 Relasi Eloquent Tetap Bisa Digunakan

Karena ini extend Eloquent Model, semua fitur relasi tetap tersedia:

```php id="g0m5z9"
public function posts()
{
    return $this->hasMany(Post::class);
}
```

---

# ⚠ Perilaku Penting Sesuai Implementasi

✔ Database wajib aktif sebelum model dipakai
✔ Throw RuntimeException jika DB tidak aktif
✔ Throw RuntimeException jika Capsule tidak tersedia
✔ Tidak ada konfigurasi koneksi di dalam class ini
✔ Bergantung pada Database bootstrap di Application

---

# 📌 Kapan Error Akan Terjadi?

1. Config db.enabled = false
2. Koneksi database gagal
3. Illuminate Database belum terinstall
4. Capsule belum di-bootstrap