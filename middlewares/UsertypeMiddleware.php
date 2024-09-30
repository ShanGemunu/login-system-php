<?php

namespace app\middlewares;

use app\core\Application;
use app\exceptions\ForbiddenException;

class UsertypeMiddleware extends BaseMiddleware
{
    protected array $actions = [];

    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    { 
        if(isset($this->actions[Application::$userType])){
            if (in_array(Application::$app->controller->action, $this->actions[Application::$userType])) {
                throw new ForbiddenException();
            }
        }
    }
}