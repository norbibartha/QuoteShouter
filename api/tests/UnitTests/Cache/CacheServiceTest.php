<?php

namespace UnitTests\Cache;

use App\Cache\CacheService;
use ReflectionException;
use UnitTests\BaseTestCase;

class CacheServiceTest extends BaseTestCase
{
    private CacheService $cacheService;

    protected function setUp(): void
    {
        $this->cacheService = new CacheService();
    }

    /**
     * @dataProvider generateCachingKeyDataProvider
     *
     * @param array $keyItems
     * @param string $expectedResult
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testGenerateCachingKey(array $keyItems, string $expectedResult): void
    {
        $result = $this->invokeMethod($this->cacheService, 'generateCachingKey', [$keyItems]);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function generateCachingKeyDataProvider(): array
    {
        return [
            [
                ['authorName' => 'test-name', 'limit' => 4],
                '7dc509d3ce08696fc7b1cf9f6b0f9073',
            ]
        ];
    }
}