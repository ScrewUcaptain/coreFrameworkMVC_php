<?php

namespace app\core\exception;

use Exception;

class ForbiddenException extends Exception
{
    protected $message = 'You don\'t have the permission to access this page';
    protected $code = 403; 
}