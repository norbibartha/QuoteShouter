<?php

namespace UnitTests;

use Consumer\ConsumeCommand;
use ErrorException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use ReflectionException;

class ConsumeCommandTest extends BaseTestCase
{
    private ConsumeCommand $command;

    protected function setUp(): void
    {
        // Disable only original constructor
        $this->command = $this->createPartialMock(ConsumeCommand::class, []);
    }

    /**
     * @dataProvider consumeMessageDataProvider
     *
     * @param AMQPStreamConnection $connection
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConsumeMessage(AMQPStreamConnection $connection): void
    {
        $this->invokeMethod($this->command, 'consumeMessage', [$connection]);

        // If there is no exception, the method works as expected
        $this->assertTrue(true);
    }

    /**
     * @return array
     */
    public function consumeMessageDataProvider(): array
    {
        $channel = $this->createMock(AMQPChannel::class);

        $connection = $this->createMock(AMQPStreamConnection::class);
        $connection->method('channel')->willReturn($channel);

        return [
            [
                'connection' => $connection,
            ]
        ];
    }

    /**
     * @dataProvider cleanupConnectionDataProvider
     *
     * @param AMQPStreamConnection $connection
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testCleanupConnection(AMQPStreamConnection $connection): void
    {
        $this->invokeMethod($this->command, 'cleanupConnection', [$connection]);

        // If there is no exception, the method works as expected
        $this->assertTrue(true);
    }

    /**
     * @return array
     */
    public function cleanupConnectionDataProvider(): array
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $exception = new ErrorException('Test exception.');
        $connection->method('close')->willThrowException($exception);

        return [
            'Simple case' => [
                'connection' => $this->createMock(AMQPStreamConnection::class),
            ],
            // Won't be thrown any exception because we ignore this type of exception
            'Case with exception' => [
                'connection' => $connection,
            ]
        ];
    }
}