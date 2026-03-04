# 📘 CaracalPHP – Scheduler Usage Documentation

`Caracal\Core\Scheduler` adalah sistem penjadwalan task berbasis file yang memungkinkan Anda menjalankan perintah secara asynchronous menggunakan cron system.

Scheduler dirancang untuk:

* Ringan
* Tidak membutuhkan database
* Tidak membutuhkan Redis
* Cocok untuk shared hosting
* Production-ready untuk aplikasi modular

---

# 1. Cara Kerja Scheduler

Scheduler bekerja dengan mekanisme berikut:

```
1. Task didaftarkan melalui Scheduler::add()
2. Task disimpan di storage/scheduler/tasks.php
3. Cron system memanggil scheduler runner
4. Scheduler mengecek task yang "due"
5. Task dijalankan sebagai proses terpisah (async)
```

---

# 2. Lokasi Penyimpanan

Scheduler otomatis membuat folder:

```text
storage/scheduler/
```

File yang dibuat otomatis:

```text
storage/scheduler/tasks.php
storage/scheduler/scheduler.log
```

Anda tidak perlu membuat file ini secara manual.

---

# 3. Menambahkan Task

Method:

```php
public function add(
    string $name,
    string $command,
    string $cron = '* * * * *',
    bool $enabled = true,
    bool $preventOverlap = true
): void
```

Contoh:

```php
use Caracal\Core\Scheduler;

$scheduler = new Scheduler();

$scheduler->add(
    'cleanup',
    'artisan cleanup.php',
    'daily'
);
```

---

# 4. Cron Expression

Scheduler mendukung:

### Preset

| Preset  | Cron Equivalent |
| ------- | --------------- |
| hourly  | 0 * * * *       |
| daily   | 0 0 * * *       |
| weekly  | 0 0 * * 0       |
| monthly | 0 0 1 * *       |

Contoh:

```php
$scheduler->add('report', 'artisan report.php', 'weekly');
```

---

### Custom Cron Expression

Format standar:

```
* * * * *
│ │ │ │ │
│ │ │ │ └─ Day of week (0-6)
│ │ │ └─── Month (1-12)
│ │ └───── Day of month (1-31)
│ └─────── Hour (0-23)
└───────── Minute (0-59)
```

Contoh setiap 5 menit:

```php
$scheduler->add(
    'send-mails',
    'artisan send:emails',
    '*/5 * * * *'
);
```

---

# 5. Menjalankan Task yang Due

Method:

```php
$scheduler->runDue();
```

Biasanya dipanggil dari file CLI:

```php
// schedule.php

require 'vendor/autoload.php';

use Caracal\Core\Scheduler;

$scheduler = new Scheduler();
$scheduler->runDue();
```

---

# 6. Integrasi dengan System Cron (Linux)

Tambahkan ke crontab:

```bash
* * * * * php /path-to-project/schedule.php
```

Scheduler akan berjalan setiap menit dan mengeksekusi task yang sesuai jadwal.

---

# 7. Menjalankan Task Secara Manual

```php
$scheduler->run('cleanup');
```

Jika task tidak ditemukan, akan melempar exception.

---

# 8. Prevent Overlapping

Jika `preventOverlap = true`:

* Scheduler membuat file lock
* Task tidak akan dijalankan jika masih berjalan
* Mencegah duplicate execution

Contoh:

```php
$scheduler->add(
    'backup',
    'artisan backup.php',
    'daily',
    true,
    true
);
```

---

# 9. Enable / Disable Task

Anda bisa menonaktifkan task saat pendaftaran:

```php
$scheduler->add(
    'temporary-task',
    'artisan temp.php',
    'daily',
    false
);
```

Task dengan `enabled = false` tidak akan dijalankan.

---

# 10. Logging

Setiap task yang dijalankan akan dicatat di:

```text
storage/scheduler/scheduler.log
```

Contoh isi log:

```
[2026-03-04 00:00:00] Task [cleanup] started.
```

---

# 11. Struktur tasks.php

File ini dibuat otomatis dan berisi konfigurasi task:

```php
<?php return [
    'cleanup' => [
        'command' => 'artisan cleanup.php',
        'cron' => '0 0 * * *',
        'enabled' => true,
        'prevent_overlap' => true,
        'last_run' => 1700000000
    ]
];
```

Anda tidak perlu mengedit file ini secara manual.