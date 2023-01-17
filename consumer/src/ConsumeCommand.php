<?php

namespace Consumer;

use Closure;
use ErrorException;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Message\AMQPMessage;
use RuntimeException;

class ConsumeCommand
{
    private const WAIT_BEFORE_RECONNECT = 30;

    public function __construct(private MessageProcessor $messageProcessor)
    {
    }

    /**
     * Run infinite loop to be ready to process the messages when they appear
     *
     * @return void
     *
     * @throws Exception
     */
    public function execute(): void
    {
        $connection = null;
        while (true) {
            try {
                $connection = $this->establishConnection();
                $this->consumeMessage($connection);
            } catch(AMQPRuntimeException $exception) {
                echo $exception->getMessage();

                $this->cleanupConnection($connection);
                // Wait 30 seconds before reconnect
                sleep(self::WAIT_BEFORE_RECONNECT);
            } catch(RuntimeException $exception) {
                $this->cleanupConnection($connection);
                // Wait 30 seconds before reconnect
                sleep(self::WAIT_BEFORE_RECONNECT);
            } catch(ErrorException $exception) {
                $this->cleanupConnection($connection);
                // Wait 30 seconds before reconnect
                sleep(self::WAIT_BEFORE_RECONNECT);
            }
        }
    }

    /**
     * @param AMQPStreamConnection $connection
     *
     * @return void
     */
    private function consumeMessage(AMQPStreamConnection $connection): void
    {
        $channel = $connection->channel();
        $channel->queue_declare(queue: getenv('RABBITMQ_QUEUE'), auto_delete: false);

        echo 'Waiting for messages...' . PHP_EOL;

        $callback = Closure::fromCallable([$this, 'processMessage']);

        $channel->basic_consume(queue: getenv('RABBITMQ_QUEUE'), no_ack: true, callback: $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * @param AMQPMessage $message
     *
     * @return void
     */
    private function processMessage(AMQPMessage $message): void
    {
        echo 'Message received...' . $message->body;

        $this->messageProcessor->process($message);
    }

    /**
     * Connect to RabbitMq
     *
     * @return AMQPStreamConnection
     *
     * @throws Exception
     */
    private function establishConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            (int)getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PASSWORD'),
        );
    }

    /**
     * Close RabbitMq connection
     *
     * @param AMQPStreamConnection $connection
     *
     * @return void
     *
     * @throws Exception
     */
    private function cleanupConnection(AMQPStreamConnection $connection): void
    {
        /*
         * Connection might already be closed.
         * Ignoring exceptions.
         */
        try {
            $connection?->close();
        } catch (ErrorException $exception) {
        }
    }
}