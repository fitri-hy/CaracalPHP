<?php
namespace App\Modules\Welcome\Controllers;

use Caracal\Core\Controller;

class WelcomeController extends Controller
{
    public function index(): string
    {

        return $this->render('Welcome/Views/welcome.view.php', [
            'title'   => 'Welcome',
            'message' => 'Welcome to the Caracal Fraemwork',
        ]);
    }
}