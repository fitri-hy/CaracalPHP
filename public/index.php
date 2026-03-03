<?php
require __DIR__ . '/../vendor/autoload.php';

use Caracal\Core\Application;

$app = Application::getInstance();
$app->run();