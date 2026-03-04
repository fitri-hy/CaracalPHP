# 📘 CaracalPHP – Queue Documentation

Class:

```php
Caracal\Core\Queue
```

`Queue` adalah sistem job queue sederhana berbasis file yang memungkinkan:

* Menyimpan job ke storage
* Menjalankan job secara asynchronous
* Memproses satu job atau semua job

Queue ini **tidak menggunakan database atau Redis**, melainkan file `.job`.

---

# 🎯 Tujuan Queue

* Menjalankan task berat di background
* Tidak menghambat HTTP request
* Menyediakan sistem antrian ringan tanpa service eksternal

---

# 1️⃣ Lokasi Penyimpanan

Semua job disimpan di:

```
/storage/jobs
```

Jika folder belum ada, akan otomatis dibuat:

```php
mkdir($this->storage, 0755, true);
```

---

# 2️⃣ Method push()

```php
public function push(callable $job, array $data = []): string
```

Digunakan untuk memasukkan job ke antrian.

---

## Cara Kerja

1. Generate UUID v4
2. Buat file `{uuid}.job`
3. Simpan hasil serialize dari:

   * Callable
   * Data

```php
serialize([$job, $data]);
```

---

## Return

Mengembalikan ID job (UUID string).

---

## Contoh Penggunaan

```php
use Caracal\Core\Queue;

$queue = new Queue();

$jobId = $queue->push(function ($data) {
    file_put_contents('log.txt', $data['message'] . PHP_EOL, FILE_APPEND);
}, [
    'message' => 'Hello Queue'
]);

echo "Job ID: " . $jobId;
```

---

# 3️⃣ Method process()

```php
public function process(string $id): bool
```

Digunakan untuk memproses satu job berdasarkan ID.

---

## Cara Kerja Internal

1. Cek apakah file job ada
2. Unserialize file
3. Generate inline PHP code
4. Jalankan dengan Symfony Process:

```php
new Process([PHP_BINARY, '-r', $phpCode]);
```

5. Hapus file job setelah dijalankan

---

## Penting

Process dijalankan dengan:

```php
$process->start();
```

Artinya:

* Non-blocking
* Asynchronous
* Tidak menunggu selesai

---

## Return

* `true` → jika job ditemukan dan dijalankan
* `false` → jika file tidak ada

---

# 4️⃣ Method processAll()

```php
public function processAll(): void
```

Digunakan untuk memproses semua file `.job` di storage.

---

## Cara Kerja

```php
foreach (glob('*.job') as $file)
```

Kemudian panggil:

```php
$this->process($id);
```

---

# 📌 Contoh Worker Sederhana

Misalnya buat file CLI:

```php
$queue = new Queue();
$queue->processAll();
```

Bisa dijalankan via cron:

```
* * * * * php worker.php
```

---

# 📌 Format File Job

Isi file `.job` adalah:

```php
serialize([$job, $data]);
```

Contoh isi setelah unserialize:

```php
[
    Closure,
    ['message' => 'Hello Queue']
]
```