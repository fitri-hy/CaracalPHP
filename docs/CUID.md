# 📘 CaracalPHP – CUID Usage Documentation

`Caracal\Core\CUID` adalah sistem pembuat ID unik berbasis cluster yang dirancang untuk aplikasi modular dan distributed system.

CUID dirancang untuk:

* Ringan
* Tidak membutuhkan database
* Tidak membutuhkan Redis
* Tidak membutuhkan service eksternal
* Aman untuk high concurrency
* Production-ready

---

# 1. Cara Kerja CUID

CUID bekerja dengan mekanisme berikut:

```
1. Server dikonfigurasi dengan datacenter & worker ID
2. CUID mengambil timestamp microsecond
3. CUID menambahkan sequence anti-collision
4. CUID menambahkan entropy random 4 byte
5. Data dikemas menjadi 16 byte binary
6. Binary diencode menjadi Base62 string
```

Hasilnya adalah ID yang:

* Unik lintas server
* Bisa diurutkan berdasarkan waktu
* Bisa di-decode kembali
* Bisa dikonversi ke UUID

---

# 2. Konfigurasi Cluster (WAJIB)

Sebelum menggunakan CUID, identitas server harus diatur:

```php
\CUID::configure(1, 9);
```

Parameter:

```php
datacenter (0–255)
worker     (0–255)
```

⚠ Setiap server wajib memiliki kombinasi berbeda.
Disarankan dipanggil di:

* bootstrap file
* BaseController
* service provider

---

# 3. Generate ID (String Mode)

```php
$id = \CUID::id();
```

Contoh output:

```
7JYYB0wlaiUDDnBaGux
```

Gunakan untuk:

* Order ID
* Invoice ID
* Transaction ID
* Tracking ID
* API Request ID

---

# 4. Generate Binary ID

Jika ingin menyimpan dalam bentuk raw binary:

```php
$binary = \CUID::binary();
```

Untuk mengubah kembali ke string:

```php
$id = \CUID::fromBinary($binary);
```

Cocok untuk:

* Penyimpanan BINARY(16)
* Optimasi index database
* High scale system

---

# 5. Validasi ID

```php
$isValid = \CUID::isValid($id);
```

Return:

```
true / false
```

---

# 6. Decode ID

## Decode dari String (Base62)

```php
$data = \CUID::decodeId($id);
```

Contoh hasil:

```php
[
  'timestamp_micro' => 1772672673843082,
  'datacenter'      => 1,
  'worker'          => 9,
  'sequence'        => 0,
  'entropy'         => '713e62a7',
  'version'         => '1.0.0'
]
```

---

## Decode dari Binary

```php
$data = \CUID::decode($binary);
```

Digunakan saat ID disimpan sebagai `BINARY(16)`.

---

# 7. Timestamp Handling

## Ambil Timestamp dari ID

```php
$timestamp = \CUID::timestampFromId($id);
```

Timestamp dikembalikan dalam **microsecond**.

---

## Format ke Datetime

```php
$datetime = \CUID::datetime($binary);
```

Output:

```
2026-03-05 08:04:33.843050
```

---

# 8. UUID Compatibility Mode

CUID dapat dikonversi menjadi format UUID standar:

```php
$uuid = \CUID::uuid($binary);
```

Contoh output:

```
00004218-6ce8-c78a-0109-0000713e62a7
```

Digunakan untuk:

* Integrasi sistem berbasis UUID
* Interoperability
* Export standar 36 karakter

---

# 9. Sharding Helper

Untuk pembagian database shard:

```php
$shard = \CUID::shard($id, 32);
```

Artinya ID akan masuk shard 0–31.
Cocok untuk:

* Partitioned table
* Multi-database architecture
* Horizontal scaling

---

# 10. Node Information

Untuk melihat konfigurasi aktif:

```php
$node = \CUID::node();
```

Output:

```php
[
  'datacenter' => 1,
  'worker'     => 9
]
```

---

# 11. Benchmark

Untuk mengukur performa generator:

```php
echo \CUID::benchmark(10000);
```

Contoh:

```
1.4 ms (1000 IDs)
```

---

# 12. Struktur Internal ID

CUID terdiri dari 16 byte:

| Field      | Size |
| ---------- | ---- |
| Timestamp  | 8B   |
| Datacenter | 1B   |
| Worker     | 1B   |
| Sequence   | 2B   |
| Entropy    | 4B   |

Kemudian diencode ke Base62 string.

---

# 13. Rekomendasi Database

## Opsi 1 – String

```sql
id VARCHAR(24) PRIMARY KEY
```

## Opsi 2 – Binary (Recommended)

```sql
id BINARY(16) PRIMARY KEY
```

---

# 14. Best Practice

### Gunakan untuk:

* Primary key publik
* Distributed system
* Microservices
* High-concurrency API
* Event tracing

### Jangan gunakan untuk:

* Password reset token
* Authentication token
* Cryptographic secret

---

# 15. Ringkasan Penggunaan Dasar

```php
// Konfigurasi node
\CUID::configure(1, 42);

// Generate 1 CUID
\CUID::binary();
\CUID::fromBinary($cuidBinary);

// Decode
\CUID::decode($cuidBinary);
\CUID::decodeId($cuidId);

// Timestamp & Datetime
\CUID::timestampFromId($cuidId);
\CUID::datetime($cuidBinary);

// UUID format
\CUID::uuid($cuidBinary);

// Validasi & Shard 
\CUID::isValid($cuidId);
\CUID::shard($cuidId);

// Node & Version
\ CUID::node();
\CUID::version();

// Benchmark test ---
\CUID::benchmark(1000);
```