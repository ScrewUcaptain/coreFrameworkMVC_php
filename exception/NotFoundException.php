<?php

namespace suc\phpmvc\exception;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Ressource not Found';
    protected $code = 404;
}
