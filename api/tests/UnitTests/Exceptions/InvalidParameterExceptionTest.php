<?php

namespace UnitTests\Exceptions;

use App\Exceptions\InvalidParameterException;
use PHPUnit\Framework\TestCase;

class InvalidParameterExceptionTest extends TestCase
{
    public function testGetMessage(): void
    {
        $exception = new InvalidParameterException();
        $this->assertSame('Invalid parameter.', $exception->getMessage());
    }
}