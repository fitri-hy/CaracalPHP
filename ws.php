<?php
require __DIR__ . '/vendor/autoload.php';

use Caracal\Core\WebSockets;

$ws = new WebSockets(8080);
$ws->run();