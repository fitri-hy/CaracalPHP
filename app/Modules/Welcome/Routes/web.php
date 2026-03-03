<?php
use App\Modules\Welcome\Controllers\WelcomeController;

return [
    ['GET', '/', WelcomeController::class . '@index'],
];