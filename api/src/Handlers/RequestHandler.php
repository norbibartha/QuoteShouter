<?php

namespace App\Handlers;

use App\Cache\RabbitMqService;
use App\Cache\RedisService;
use App\Database\MySqlClientFactory;
use App\Exceptions\HttpMethodNotAllowedException;
use App\Services\DatabaseService;
use App\Services\FileProcessor;
use App\Services\QuotesService;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Predis\Client;

require __DIR__ . '/../../src/includes/routes.php';

class RequestHandler
{
    /**
     * Based on request data find the proper class and action, load the class and execute the action
     *
     * @param string $uri
     * @param string $requestMethod
     * @param string|null $requestBody
     *
     * @return void
     */
    public function handleRequest(string $uri, string $requestMethod, ?string $requestBody): void
    {
        $parameters = $this->extractRequestParameters($uri, $requestBody);

        foreach (ROUTES as $route) {
            try {
                if ($route['method'] !== strtoupper($requestMethod)) {
                    throw new HttpMethodNotAllowedException();
                }

                preg_match($route['pattern'], $uri, $matches);
                if (empty($matches)) {
                    continue;
                }

                $action = $route['action'];
                $className = 'App\Controller\\' . $action['controller'];
                $serviceGetter = sprintf('get%sServices', $action['controller']);
                $services = $this->$serviceGetter();
                $controller = new $className(...$services);
                $methodName = $action['methodName'];
                $controller->{$methodName}($parameters);

                exit(0);
            } catch (HttpMethodNotAllowedException $exception) {
                header('HTTP/1.1 405 Method Not Allowed');
                exit(1);
            }
        }

        header('HTTP/1.1 404 Not Found');
        exit(2);
    }

    /**
     * Extract parameters from request
     *
     * @param string $uri
     * @param string|null $requestBody
     *
     * @return array
     */
    private function extractRequestParameters(string $uri, ?string $requestBody): array
    {
        $uri = strtolower($uri);
        $urlComponents = parse_url($uri);
        parse_str($urlComponents['query'] ?? '', $queryParams);
        $url = parse_url($uri, PHP_URL_PATH);
        $pathVariables = explode( '/', $url);

        $parameters = [
            'pathVariables' => $pathVariables,
        ];

        if (!empty($queryParams)) {
            $parameters['queryParams'] = $queryParams;
        }

        if (!empty($requestBody)) {
            $parameters['body'] = json_decode($requestBody, true);
        }

        return $parameters;
    }

    /**
     * Load necessary services for QuotesController
     *
     * @return array
     *
     * @throws Exception
     */
    private function getQuotesControllerServices(): array
    {
        $databaseClient = MySqlClientFactory::getClient();

        $databaseService = new DatabaseService($databaseClient);
        $fileProcessor = new FileProcessor();

        $redisClient = new Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => (int)getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASSWORD'),
        ]);
        $redisService = new RedisService($redisClient);

        $rabbitMqConnection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            (int)getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PASSWORD'),
        );
        $rabbitMqService = new RabbitMqService($rabbitMqConnection);

        $quotesService = new QuotesService($databaseService, $fileProcessor, $redisService, $rabbitMqService);

        return [$quotesService];
    }
}