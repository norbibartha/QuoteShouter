<?php

use Consumer\ConsumeCommand;
use Consumer\MessageProcessor;
use Predis\Client;

require_once __DIR__ . '/../vendor/autoload.php';

// Create RedisClient connection, parameters passed using a named array
$redisClient = new Client([
    'scheme' => getenv('REDIS_SCHEME'),
    'host' => getenv('REDIS_HOST'),
    'port' => (int)getenv('REDIS_PORT'),
    'password' => getenv('REDIS_PASSWORD'),
]);
$messageProcessor = new MessageProcessor($redisClient);

$command = new ConsumeCommand($messageProcessor);
$command->execute();