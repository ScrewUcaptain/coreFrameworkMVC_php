<?php

namespace app\core;

use app\core\middlewares\BaseMiddleware;

class BaseController
{
    public string $layout = 'main';
    public string $action;
    protected array $middlewares = [];

    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view,$params);
    }
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

     public function getMiddlewares(): array
     {
        return $this->middlewares;
     }

     public function setMiddlewares($middleware)
     {
        $this->middlewares = $middleware;
     }
}