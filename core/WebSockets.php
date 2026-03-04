<?php
namespace Caracal\Core;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use React\Socket\SecureServer;

class WebSockets
{
    protected string $host;
    protected int $port;
    protected bool $useSSL;
    protected ?string $certPath;
    protected ?string $keyPath;
    protected bool $logging;
    protected bool $authEnabled;
    protected string $authSecret;
    protected int $pingInterval;

    protected IoServer $server;

    public function __construct()
    {
        $config = \Caracal\Core\Application::getInstance()->config();

        $this->host = $config->get('ws.host', '0.0.0.0');
        $this->port = $config->get('ws.port', 8080);
        $this->logging = $config->get('ws.logging', true);

        $this->useSSL = $config->get('ws.use_ssl', false);
        $this->certPath = $config->get('ws.cert_path', null);
        $this->keyPath = $config->get('ws.key_path', null);

        $this->authEnabled = $config->get('ws.auth_enabled', false);
        $this->authSecret = $config->get('ws.auth_secret', '');
        $this->pingInterval = (int)$config->get('ws.ping_interval', 30);

        if ($this->authEnabled && empty($this->authSecret)) {
            throw new \Exception("WS_AUTH_SECRET must be set in .env when WS_AUTH_ENABLED=true");
        }

        if (!class_exists(IoServer::class)) {
            throw new \Exception(
                "Ratchet/WebSocket package not installed. Run:\ncomposer require cboden/ratchet react/event-loop react/socket guzzlehttp/psr7"
            );
        }

        $this->setupServer();
    }

    protected function setupServer(): void
    {
        $loop = LoopFactory::create();
        $socket = new Reactor("{$this->host}:{$this->port}", $loop);

        if ($this->useSSL) {
            if (empty($this->certPath) || empty($this->keyPath)) {
                throw new \Exception("WS_CERT_PATH and WS_KEY_PATH must be set when WS_USE_SSL=true");
            }
            $socket = new SecureServer($socket, $loop, [
                'local_cert' => $this->certPath,
                'local_pk'   => $this->keyPath,
                'verify_peer'=> false
            ]);
        }

        $wsServer = new WsServer(new class($this->authEnabled, $this->authSecret, $this->logging, $this->pingInterval) implements MessageComponentInterface {
            protected \SplObjectStorage $clients;
            protected bool $authEnabled;
            protected string $authSecret;
            protected bool $logging;
            protected int $pingInterval;

            public function __construct(bool $authEnabled, string $authSecret, bool $logging, int $pingInterval)
            {
                $this->clients = new \SplObjectStorage;
                $this->authEnabled = $authEnabled;
                $this->authSecret = $authSecret;
                $this->logging = $logging;
                $this->pingInterval = $pingInterval;

                if ($this->logging) {
                    echo "WebSocket server initialized with ping interval {$this->pingInterval}s\n";
                }
            }

            public function onOpen(ConnectionInterface $conn)
            {
                if ($this->authEnabled) {
                    $headers = $conn->httpRequest->getHeader('Sec-WebSocket-Protocol');
                    if (empty($headers) || $headers[0] !== $this->authSecret) {
                        $conn->close();
                        if ($this->logging) echo "Unauthorized connection rejected\n";
                        return;
                    }
                }

                $this->clients->attach($conn);
                if ($this->logging) echo "New connection ({$conn->resourceId})\n";
            }

            public function onMessage(ConnectionInterface $from, $msg)
            {
                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send($msg);
                    }
                }
                if ($this->logging) {
                    echo "Message from {$from->resourceId}: $msg\n";
                }
            }

            public function onClose(ConnectionInterface $conn)
            {
                $this->clients->detach($conn);
                if ($this->logging) echo "Connection {$conn->resourceId} disconnected\n";
            }

            public function onError(ConnectionInterface $conn, \Exception $e)
            {
                if ($this->logging) echo "Error: {$e->getMessage()}\n";
                $conn->close();
            }
        });

        $httpServer = new HttpServer($wsServer);

        $this->server = new IoServer($httpServer, $socket, $loop);
    }

    public function run(): void
    {
        echo "WebSocket server started at " . ($this->useSSL ? 'wss' : 'ws') . "://{$this->host}:{$this->port}\n";
        if ($this->authEnabled) echo "WS Auth enabled with secret: true\n";
        $this->server->run();
    }
}