<?php
namespace App\Modules\Test\Controllers;

use Caracal\Core\Controller;

class TestController extends Controller
{
    public function index(): string
    {

        return $this->render('Test/Views/test.view.php', [
            'title'   => 'Test',
            'message' => 'Test to the Caracal Fraemwork',
        ]);
    }
}