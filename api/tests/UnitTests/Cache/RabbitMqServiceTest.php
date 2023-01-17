<?php

namespace UnitTests\Cache;

use App\Cache\RabbitMqService;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class RabbitMqServiceTest extends TestCase
{
    private RabbitMqService $rabbitMqService;
    private AMQPStreamConnection $rabbitMqConnection;

    protected function setUp(): void
    {
        $this->rabbitMqConnection = $this->createMock(AMQPStreamConnection::class);
        $this->rabbitMqService = new RabbitMqService($this->rabbitMqConnection);
    }

    /**
     * @dataProvider publishDataProvider
     *
     * @param array $responseIdentifier
     * @param array $data
     * @param AMQPChannel $channel
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPublish(array $responseIdentifier, array $data, AMQPChannel $channel): void
    {
        $this->rabbitMqConnection->method('channel')->willReturn($channel);
        $this->rabbitMqService->publish($responseIdentifier, $data);

        // If there is no any exception the method works properly
        $this->assertTrue(true);
    }

    public function publishDataProvider(): array
    {
        return [
            [
                'responseIdentifier' => ['authorName' => 'test name', 'limit' => 4],
                'data' => ['QUOTE1 TEST!', 'QUOTE2 TEST!'],
                'channel' => $this->createMock(AMQPChannel::class),
            ]
        ];
    }
}