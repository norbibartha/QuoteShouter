<?php

namespace UnitTests;

use Consumer\MessageProcessor;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class MessageProcessorTest extends TestCase
{
    private MessageProcessor $messageProcessor;

    protected function setUp(): void
    {
        $redisClient = $this->createMock(Client::class);
        $this->messageProcessor = new MessageProcessor($redisClient);
    }

    /**
     * @dataProvider processDataProvider
     *
     * @param AMQPMessage $message
     *
     * @return void
     */
    public function testProcess(AMQPMessage $message): void
    {
        $this->messageProcessor->process($message);

        // If there is no any errors the method works as expected
        $this->assertTrue(true);
    }

    /**
     * @return array
     */
    public function processDataProvider(): array
    {
        $message = $this->createMock(AMQPMessage::class);
        $message->body = '{"cachingKey":"asd3wed1edasd","quotes":["QUOTE EXAMPLE 1!","QUOTE EXAMPLE 2!"]}';

        return [
            [
                'message' => $message,
            ]
        ];
    }
}