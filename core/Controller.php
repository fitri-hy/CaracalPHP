<?php
namespace Caracal\Core;

class Controller
{
    protected Request $request;
    protected View $view;
    protected Asset $asset;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->view    = new View();
        $this->asset   = new Asset();
    }

    protected function render(string $tpl, array $data = []): string
    {
        $data['asset'] = $this->asset;
        return $this->view->render($tpl, $data);
    }
}