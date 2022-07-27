<?php 

namespace suc\phpmvc\middlewares;

abstract class BaseMiddleware
{
    abstract public function execute();
}