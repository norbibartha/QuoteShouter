<?php

namespace UnitTests\Exceptions;

use App\Exceptions\HttpMethodNotAllowedException;
use PHPUnit\Framework\TestCase;

class HttpMethodNotAllowedExceptionTest extends TestCase
{
    public function testGetMessage(): void
    {
        $exception = new HttpMethodNotAllowedException();
        $this->assertSame('Method not allowed.', $exception->getMessage());
    }
}