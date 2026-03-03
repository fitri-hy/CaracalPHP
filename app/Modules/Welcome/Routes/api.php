<?php
use App\Modules\Welcome\Controllers\WelcomeApiController;

return [
    ['GET', '/api/welcome',   WelcomeApiController::class . '@index'],
];