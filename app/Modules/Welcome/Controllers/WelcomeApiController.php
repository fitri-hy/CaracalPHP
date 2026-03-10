<?php
namespace App\Modules\Welcome\Controllers;

use Caracal\Core\Controller;

class WelcomeApiController extends Controller
{
    public function index(): void
    {
        $this->json([
            'success' => true,
            'message' => 'Welcome to the Caracal API!'
        ]);
    }
}