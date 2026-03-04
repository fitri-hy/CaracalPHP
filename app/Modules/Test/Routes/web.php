<?php
use App\Modules\Test\Controllers\TestController;

return [
    ['GET', '/test', TestController::class . '@index'],
];