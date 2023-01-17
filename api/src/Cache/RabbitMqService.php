<?php

namespace App\Cache;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqService extends CacheService
{
    /**
     * @throws Exception
     */
    public function __construct(private AMQPStreamConnection $connection)
    {
    }

    /**
     * Push message into RabbitMq queue
     *
     * @param array $responseIdentifier
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function publish(array $responseIdentifier, array $data): void
    {
        $channel = $this->connection->channel();

        $channel->queue_declare(queue: getenv('RABBITMQ_QUEUE'), auto_delete: false);

        $cachingKey = $this->generateCachingKey($responseIdentifier);
        $message = ['cachingKey' => $cachingKey, 'quotes' => $data];

        $amqpMessage = new AMQPMessage(json_encode($message));
        $channel->basic_publish(msg: $amqpMessage, routing_key: getenv('RABBITMQ_QUEUE'));

        $channel->close();
        $this->connection->close();
    }
}