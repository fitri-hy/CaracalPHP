# 📘 CaracalPHP – Event System Documentation

Class:

```php
Caracal\Core\Event
```

Event system ini adalah **static event dispatcher sederhana** yang mendukung:

* Event listener biasa (`on`)
* Event listener sekali jalan (`once`)
* Trigger event
* Hapus listener (`off`)

---

# 🎯 Konsep Dasar

Event bekerja dengan pola:

```
Register Listener → Trigger Event → Jalankan Callback
```

Semua method bersifat **static**, sehingga tidak perlu membuat instance.

---

# 1️⃣ Mendaftarkan Event Listener

Gunakan:

```php
Event::on(string $event, callable $callback);
```

---

## Contoh

```php
use Caracal\Core\Event;

Event::on('user.registered', function ($data) {
    return 'User registered: ' . $data['email'];
});
```

Listener ini akan dijalankan setiap kali event dipicu.

---

# 2️⃣ Listener Sekali Jalan (Once)

Gunakan:

```php
Event::once(string $event, callable $callback);
```

Listener ini:

* Hanya dijalankan sekali
* Setelah dipanggil akan otomatis dihapus

---

## Contoh

```php
Event::once('user.login', function ($data) {
    return 'First login detected';
});
```

---

# 3️⃣ Menjalankan Event

Gunakan:

```php
Event::trigger(string $event, array $data = []): array
```

---

## Contoh

```php
$results = Event::trigger('user.registered', [
    'email' => 'john@example.com'
]);
```

Return value:

```php
array
```

Berisi hasil return dari setiap callback.

Contoh hasil:

```php
[
    'User registered: john@example.com'
]
```

---

# 4️⃣ Menghapus Listener

Gunakan:

```php
Event::off(string $event, ?callable $callback = null);
```

---

## 🔹 Hapus Semua Listener untuk Event

```php
Event::off('user.registered');
```

Akan menghapus:

* Listener biasa
* Listener once

---

## 🔹 Hapus Listener Tertentu

```php
$callback = function ($data) {
    return 'Test';
};

Event::on('test.event', $callback);

Event::off('test.event', $callback);
```

Hanya callback tersebut yang dihapus.

---

# 5️⃣ Cara Kerja Internal

Event menyimpan listener dalam dua array static:

```php
protected static array $listeners = [];
protected static array $onceListeners = [];
```

Saat `trigger()` dipanggil:

1. Jalankan semua `$listeners`
2. Jalankan semua `$onceListeners`
3. Hapus `$onceListeners` untuk event tersebut
4. Return array hasil callback

---

# 6️⃣ Contoh Penggunaan di Aplikasi

## 🔹 Contoh: Setelah User Register

```php
Event::on('user.registered', function ($data) {
    // kirim email
});

Event::trigger('user.registered', [
    'email' => 'john@example.com'
]);
```

---

## 🔹 Contoh: Logging

```php
Event::on('db.query.executed', function ($data) {
    file_put_contents('log.txt', $data['query'] . PHP_EOL, FILE_APPEND);
});
```

---

# 📌 Perilaku Penting

✔ Listener disimpan secara static (global state)
✔ Listener menerima satu parameter `$data` (array)
✔ Return callback dikumpulkan dalam array
✔ `once()` otomatis dihapus setelah trigger
✔ `off()` bisa hapus semua atau spesifik callback

---

# ⚠ Hal yang Perlu Dipahami

* Listener dibandingkan menggunakan `===`
* Jika callback berupa closure berbeda instance → tidak dianggap sama
* Event name bersifat string literal (case-sensitive)

---

# 📌 Ringkasan Method

| Method    | Fungsi                |
| --------- | --------------------- |
| on()      | Daftarkan listener    |
| once()    | Listener sekali jalan |
| trigger() | Jalankan event        |
| off()     | Hapus listener        |