<?php

namespace Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use Predis\Client;

class MessageProcessor
{
    // 12 hours in seconds
    private const CACHE_EXPIRATION_TIME = 60 * 60 * 12;

    public function __construct(private Client $redisClient)
    {
    }

    /**
     * Write message into Redis
     *
     * @param AMQPMessage $message
     *
     * @return void
     */
    public function process(AMQPMessage $message): void
    {
        $message = json_decode($message->body, true);
        $key = $message['cachingKey'];
        $value = json_encode($message['quotes']);

        $this->redisClient->set($key, $value, 'ex', self::CACHE_EXPIRATION_TIME);
    }
}