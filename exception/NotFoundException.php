<?php

namespace app\core\exception;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Ressource not Found';
    protected $code = 404;
}
