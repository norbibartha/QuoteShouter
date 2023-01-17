<?php

namespace UnitTests\Cache;

use App\Cache\RedisService;
use Predis\Client;
use ReflectionException;
use UnitTests\BaseTestCase;

class RedisServiceTest extends BaseTestCase
{
    private RedisService $redisService;

    protected function setUp(): void
    {
        $redisClient = $this->createMock(Client::class);
        $this->redisService = new RedisService($redisClient);
    }

    /**
     * @dataProvider normalizeResponseDataProvider
     *
     * @param string $response
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testNormalizeResponse(string $response, array $expectedResults): void
    {
        $results = $this->invokeMethod($this->redisService, 'normalizeResponse', [$response]);

        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function normalizeResponseDataProvider(): array
    {
        return [
            [
                'response' => '["QUOTE1 TEST!","QUOTE2 TEST!"]',
                'expectedResults' => ['QUOTE1 TEST!', 'QUOTE2 TEST!'],
            ]
        ];
    }
}