<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class BaseTestCase extends TestCase
{
    /**
     * @param $object
     * @param string $methodName
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    protected function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}