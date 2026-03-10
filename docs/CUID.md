# CaracalPHP – CUID Usage Documentation

Class

```php
Caracal\Core\CUID
```

`CUID` is a cluster-safe unique ID generator designed for Caracal applications.

It is intended for use in distributed systems, high concurrency environments, database indexing, and microservice tracing.

The generator produces a **16-byte binary ID** that can be encoded into a **Base62 string**.

---

## CUID Structure

The generated identifier consists of the following structure.

```
Timestamp  (8 bytes)
Datacenter (1 byte)
Worker     (1 byte)
Sequence   (2 bytes)
Entropy    (4 bytes)
```

Total size

```
16 bytes
```

The binary value is then encoded into a Base62 string representation.

---

## Node Configuration

Each server should have a unique node identity.

```php
use Caracal\Core\CUID;

CUID::configure(1, 9);
```

Parameters

```
datacenter 0–255
worker     0–255
```

This ensures that generated IDs remain unique across distributed environments.

---

## Generating an ID

```php
$id = CUID::id();
```

Example output

```
7JYYB0wlaiUDDnBaGux
```

---

## Generating Binary IDs

```php
$binary = CUID::binary();
```

Convert the binary ID back to a string identifier

```php
$id = CUID::fromBinary($binary);
```

---

## Decoding an ID

```php
$data = CUID::decodeId($id);
```

Example output

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

## Retrieving the Timestamp

```php
$timestamp = CUID::timestampFromId($id);
```

The timestamp is returned in **microseconds**.

---

## Converting to Datetime

```php
$datetime = CUID::datetime($binary);
```

Example output

```
2026-03-05 08:04:33.843050
```

---

## UUID Compatibility

A CUID binary value can also be converted into a UUID format.

```php
$uuid = CUID::uuid($binary);
```

Example output

```
00004218-6ce8-c78a-0109-0000713e62a7
```

---

## Sharding Helper

```php
$shard = CUID::shard($id, 32);
```

This method can be used for database sharding, partitioned tables, or distributed storage strategies.

---

## Node Information

```php
CUID::node();
```

Example output

```php
[
  'datacenter' => 1,
  'worker'     => 9
]
```

---

## Benchmark

```php
echo CUID::benchmark(10000);
```

Example result

```
12.4 ms (10000 IDs)
```

---

## Database Recommendations

### String Storage

```
id VARCHAR(24)
```

### Binary Storage (Recommended)

```
id BINARY(16)
```

Binary indexing generally provides significantly better performance for large datasets.