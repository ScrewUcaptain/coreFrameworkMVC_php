<?php 

namespace suc\phpmvc\middlewares;

use suc\phpmvc\Application;
use suc\phpmvc\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware // midleware for ungranted permissions to certains pages defined in the constructor like so  ['login', 'register'] to guest users 
{
    public array $actions;

    public function __construct($actions)
    {
        $this->actions = $actions;
    }

    public function execute()
     {
        if (Application::isGuest())
        {
            if(empty($this->actions) || in_array(Application::$app->controller->action, $this->actions))
            {
                throw new ForbiddenException();
            }
        }
     }
}
