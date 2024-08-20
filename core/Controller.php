<?php
namespace app\core;

use app\core\Application;

class Controller
{
    public string $layout = 'main';

    /*
        set layout for specific controller
        @params string
        @return void
        
    */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /*
        retrun specific view
        @params string, array
        @return string

    */
    public function render($view, array $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

}