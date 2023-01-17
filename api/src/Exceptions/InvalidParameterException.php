<?php

namespace App\Exceptions;

use Exception;

class InvalidParameterException extends Exception
{
    private const ERROR_MESSAGE = 'Invalid parameter.';
    public function __construct(string $message = '')
    {
        if (empty($message)) {
            $message = self::ERROR_MESSAGE;
        }
        parent::__construct($message);
    }
}