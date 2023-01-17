<?php

namespace App\Exceptions;

use Exception;

class HttpMethodNotAllowedException extends Exception
{
    private const ERROR_MESSAGE = 'Method not allowed.';
    public function __construct()
    {
        parent::__construct(self::ERROR_MESSAGE);
    }
}