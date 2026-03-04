# 📘 CaracalPHP – WebSocket Documentation

## Overview

`Caracal\Core\WebSockets` adalah wrapper **Ratchet WebSocket server** yang siap pakai untuk CaracalPHP.
Fitur:

* Broadcast pesan ke semua client
* Optional authentication via token
* Optional SSL/TLS
* Logging untuk monitoring
* Configurable ping interval

---

## 1️⃣ Persiapan `.env`

Tambahkan key WebSocket di `.env`:

```dotenv
# WEBSOCKET CONFIG
WS_HOST=0.0.0.0
WS_PORT=8080
WS_LOGGING=true
WS_USE_SSL=false
WS_CERT_PATH=       # Jika WS_USE_SSL=true
WS_KEY_PATH=        # Jika WS_USE_SSL=true
WS_AUTH_ENABLED=false
WS_AUTH_SECRET=SecretToken1234567890
WS_PING_INTERVAL=30
```

Keterangan:

| Key              | Fungsi                                                   |
| ---------------- | -------------------------------------------------------- |
| WS_HOST          | Host untuk server (default: `0.0.0.0`)                   |
| WS_PORT          | Port WebSocket (default: 8080)                           |
| WS_LOGGING       | Aktifkan logging ke console (true/false)                 |
| WS_USE_SSL       | Aktifkan WSS (true/false)                                |
| WS_CERT_PATH     | Path sertifikat SSL (jika WS_USE_SSL=true)               |
| WS_KEY_PATH      | Path private key SSL (jika WS_USE_SSL=true)              |
| WS_AUTH_ENABLED  | Aktifkan token auth (true/false)                         |
| WS_AUTH_SECRET   | Token auth (harus diisi jika WS_AUTH_ENABLED=true)       |
| WS_PING_INTERVAL | Interval ping server ke client dalam detik (default: 30) |

> ⚠ WS_AUTH_SECRET **wajib diisi** jika auth diaktifkan.

---

## 2️⃣ Cara Menggunakan

### Inisialisasi Server

```php
use Caracal\Core\WebSockets;

$ws = new WebSockets();
$ws->run();
```

Server akan otomatis mengambil konfigurasi dari `.env`.

### Output Console

```
WebSocket server started at ws://0.0.0.0:8080
WS Auth enabled with secret: true
WebSocket server initialized with ping interval 30s
```

Jika logging diaktifkan (`WS_LOGGING=true`), semua koneksi, pesan, dan error akan ditampilkan di console.

---

## 3️⃣ Client Connection

#### JavaScript Client

```javascript
// Tanpa auth
const ws = new WebSocket("ws://127.0.0.1:8080");

// Dengan auth token
const wsAuth = new WebSocket("ws://127.0.0.1:8080", "SecretToken1234567890");

ws.onopen = () => console.log("Connected to WS server");
ws.onmessage = (msg) => console.log("Message received:", msg.data);
ws.onclose = () => console.log("Disconnected from WS server");
```

> ⚡ Jika auth diaktifkan, WebSocket akan menolak koneksi tanpa token yang sesuai.

---

## 4️⃣ Broadcast & Messaging

Server otomatis melakukan **broadcast**:

* Semua pesan yang dikirim dari satu client akan diteruskan ke semua client lain
* Tidak perlu menulis handler tambahan

```javascript
ws.send("Hello everyone!");
```

---

## 5️⃣ SSL/TLS (WSS)

Jika `WS_USE_SSL=true`, pastikan `.env` mengisi:

* `WS_CERT_PATH` → file `.crt`
* `WS_KEY_PATH` → file `.key`

Contoh:

```dotenv
WS_USE_SSL=true
WS_CERT_PATH=/etc/ssl/certs/ws_cert.crt
WS_KEY_PATH=/etc/ssl/private/ws_key.key
```

Server otomatis menggunakan `wss://` saat SSL aktif.

---

## 6️⃣ Token Authentication

Aktifkan dengan:

```dotenv
WS_AUTH_ENABLED=true
WS_AUTH_SECRET=SecretToken1234567890
```

Client harus mengirim token di **Sec-WebSocket-Protocol**:

```javascript
const ws = new WebSocket("ws://127.0.0.1:8080", "SecretToken1234567890");
```

Jika token salah atau kosong, koneksi akan ditutup.

---

## 7️⃣ Ping Interval

Server akan otomatis mengirim ping ke semua client setiap `WS_PING_INTERVAL` detik.
Default: 30 detik.

---

## 8️⃣ Cara Menjalankan Server

Via CLI:

```bash
php caracal server:ws               # Jalankan server WebSocket
php caracal server:ws --port=9000   # Jalankan server di port lain
```