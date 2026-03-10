# CaracalPHP – WebSocket Documentation

Class:

```php
Caracal\Core\WebSockets
```

`WebSockets` is a ready-to-use **Ratchet WebSocket wrapper** for CaracalPHP.

Features:

* Broadcast messages to all clients
* Optional token authentication
* Optional SSL/TLS (WSS)
* Logging for monitoring
* Configurable ping interval

---

## `.env` Configuration

Add the following keys:

```dotenv
WS_HOST=0.0.0.0
WS_PORT=8080
WS_LOGGING=true
WS_USE_SSL=false
WS_CERT_PATH=       # Required if WS_USE_SSL=true
WS_KEY_PATH=        # Required if WS_USE_SSL=true
WS_AUTH_ENABLED=false
WS_AUTH_SECRET=SecretToken1234567890
WS_PING_INTERVAL=30
```

| Key              | Description                                   |
| ---------------- | --------------------------------------------- |
| WS_HOST          | Server host (default `0.0.0.0`)               |
| WS_PORT          | Server port (default `8080`)                  |
| WS_LOGGING       | Enable console logging (true/false)           |
| WS_USE_SSL       | Enable WSS (true/false)                       |
| WS_CERT_PATH     | SSL certificate path (if WSS enabled)         |
| WS_KEY_PATH      | SSL private key path (if WSS enabled)         |
| WS_AUTH_ENABLED  | Enable token authentication (true/false)      |
| WS_AUTH_SECRET   | Auth token (required if WS_AUTH_ENABLED=true) |
| WS_PING_INTERVAL | Server ping interval in seconds (default 30)  |

> `WS_AUTH_SECRET` is mandatory if authentication is enabled.

---

## Starting the Server

```php
use Caracal\Core\WebSockets;

$ws = new WebSockets();
$ws->run();
```

Console output:

```
WebSocket server started at ws://0.0.0.0:8080
WS Auth enabled with secret: true
WebSocket server initialized with ping interval 30s
```

* Logging enabled (`WS_LOGGING=true`) will show connections, messages, and errors.

---

## Client Connection

### JavaScript Client

**Without authentication:**

```javascript
const ws = new WebSocket("ws://127.0.0.1:8080");
```

**With token authentication:**

```javascript
const wsAuth = new WebSocket("ws://127.0.0.1:8080", "SecretToken1234567890");
```

Event handling:

```javascript
ws.onopen = () => console.log("Connected");
ws.onmessage = (msg) => console.log("Message received:", msg.data);
ws.onclose = () => console.log("Disconnected");
```

> Auth must match `WS_AUTH_SECRET` if enabled; otherwise connection is rejected.

---

## Broadcast & Messaging

* Messages sent by one client are **broadcast** to all other connected clients.
* No additional handler required:

```javascript
ws.send("Hello everyone!");
```

---

## SSL/TLS (WSS)

Enable SSL:

```dotenv
WS_USE_SSL=true
WS_CERT_PATH=/etc/ssl/certs/ws_cert.crt
WS_KEY_PATH=/etc/ssl/private/ws_key.key
```

* Server automatically uses `wss://` when SSL is enabled.

---

## Token Authentication

Enable in `.env`:

```dotenv
WS_AUTH_ENABLED=true
WS_AUTH_SECRET=SecretToken1234567890
```

* Client sends token in **Sec-WebSocket-Protocol**:

```javascript
const ws = new WebSocket("ws://127.0.0.1:8080", "SecretToken1234567890");
```

* Wrong or missing token → connection closed.

---

## Ping Interval

* Server sends a ping to all clients every `WS_PING_INTERVAL` seconds (default 30s).

---

## Running the Server via CLI

```bash
php caracal server:ws               # Run WebSocket server
php caracal server:ws --port=9000   # Run server on custom port
```
