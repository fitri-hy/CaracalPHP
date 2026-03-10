# CaracalPHP – CUID Usage Documentation

`Caracal\Core\CUID` adalah generator **cluster-safe unique ID** untuk aplikasi Caracal.

Dirancang untuk:

* distributed systems
* high concurrency
* database indexing
* microservices tracing

CUID menghasilkan **16 byte binary ID** yang dapat diubah menjadi **Base62 string**.

---

# Cara Kerja

Struktur CUID:

```
Timestamp  (8 byte)
Datacenter (1 byte)
Worker     (1 byte)
Sequence   (2 byte)
Entropy    (4 byte)
```

Total:

```
16 byte
```

Kemudian di-encode menjadi Base62 string.

---

# Konfigurasi Node

Setiap server harus memiliki identitas.

```php
use Caracal\Core\CUID;

CUID::configure(1, 9);
```

Parameter:

```
datacenter 0-255
worker     0-255
```

---

# Generate ID

```php
$id = CUID::id();
```

Contoh output:

```
7JYYB0wlaiUDDnBaGux
```

---

# Generate Binary

```php
$binary = CUID::binary();
```

Convert kembali:

```
$id = CUID::fromBinary($binary);
```

---

# Decode ID

```php
$data = CUID::decodeId($id);
```

Output:

```php
[
  'timestamp_micro' => 1772672673843082,
  'datacenter'      => 1,
  'worker'          => 9,
  'sequence'        => 0,
  'entropy'         => '713e62a7',
  'version'         => '1.1.0'
]
```

---

# Ambil Timestamp

```
$timestamp = CUID::timestampFromId($id);
```

Timestamp dalam **microsecond**.

---

# Convert ke Datetime

```
$datetime = CUID::datetime($binary);
```

Contoh:

```
2026-03-05 08:04:33.843050
```

---

# UUID Compatibility

```
$uuid = CUID::uuid($binary);
```

Contoh:

```
00004218-6ce8-c78a-0109-0000713e62a7
```

---

# Sharding Helper

```
$shard = CUID::shard($id, 32);
```

Digunakan untuk:

* database sharding
* partition table
* distributed storage

---

# Node Information

```
CUID::node();
```

Output:

```php
[
  'datacenter' => 1,
  'worker'     => 9
]
```

---

# Benchmark

```
echo CUID::benchmark(10000);
```

Contoh:

```
12.4 ms (10000 IDs)
```

---

# Database Recommendation

## String

```
id VARCHAR(24)
```

## Binary (Recommended)

```
id BINARY(16)
```

Binary indexing jauh lebih cepat.

---

# Best Practice

Gunakan untuk:

* Order ID
* Invoice ID
* Transaction ID
* Distributed logging
* API request tracing

Jangan gunakan untuk:

* password reset token
* authentication token
* cryptographic secrets