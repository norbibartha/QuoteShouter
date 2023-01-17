<?php

namespace IntegrationTests;

use App\Cache\RabbitMqService;
use App\Cache\RedisService;
use App\Database\DatabaseClient;
use App\Services\DatabaseService;
use App\Services\FileProcessor;
use App\Services\QuotesService;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class QuotesServiceTest extends TestCase
{
    private QuotesService $quotesService;
    private DatabaseClient $databaseClient;
    private RedisService $redisService;
    private AMQPStreamConnection $rabbitMqConnection;

    protected function setUp(): void
    {
        $this->databaseClient = $this->createMock(DatabaseClient::class);
        $databaseService = new DatabaseService($this->databaseClient);
        $fileProcessor = new FileProcessor();
        $this->redisService = $this->createMock(RedisService::class);
        $this->rabbitMqConnection = $this->createMock(AMQPStreamConnection::class);
        $rabbitMqService = new RabbitMqService($this->rabbitMqConnection);
        $this->quotesService = new QuotesService(
            $databaseService,
            $fileProcessor,
            $this->redisService,
            $rabbitMqService
        );
    }

    /**
     * @dataProvider getQuotesByAuthorWithCacheDataProvider
     *
     * @param string $authorName
     * @param int $limit
     * @param array $quotes
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetQuotesByAuthorWithCache(
        string $authorName,
        int $limit,
        array $quotes,
        array $expectedResults
    ): void {
        $this->redisService->method('getResponse')->willReturn($quotes);
        $results = $this->quotesService->getQuotesByAuthor($authorName, $limit);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function getQuotesByAuthorWithCacheDataProvider(): array
    {
        return [
            [
                'authorName' => 'robert-frost',
                'limit' => 3,
                'quotes' => ['TEST QUOTE DB 1.', 'TEST QUOTE FILE 1.', 'TEST QUOTE FILE 2.'],
                'expectedResults' => ['TEST QUOTE DB 1.', 'TEST QUOTE FILE 1.', 'TEST QUOTE FILE 2.'],
            ]
        ];
    }

    /**
     * @dataProvider getQuotesByAuthorWithoutCacheDataProvider
     *
     * @param string $authorName
     * @param int $limit
     * @param array|null $redisResponse
     * @param array $databaseResponse
     * @param AMQPChannel $channel
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetQuotesByAuthorWithoutCache(
        string $authorName,
        int $limit,
        ?array $redisResponse,
        array $databaseResponse,
        AMQPChannel $channel,
        array $expectedResults
    ): void {
        $this->redisService->method('getResponse')->willReturn($redisResponse);
        $this->databaseClient->method('select')->willReturn($databaseResponse);
        $this->rabbitMqConnection->method('channel')->willReturn($channel);
        $results = $this->quotesService->getQuotesByAuthor($authorName, $limit);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function getQuotesByAuthorWithoutCacheDataProvider(): array
    {
        return [
            [
                'authorName' => 'robert-frost',
                'limit' => 3,
                'redisResponse' => null,
                'databaseResponse' => [['text' => 'Test quote db 1.'], ['text' => 'Test quote db 2.'], ['text' => 'Test quote from database...']],
                'channel' => $this->createMock(AMQPChannel::class),
                'expectedResults' => ['TEST QUOTE DB 1!', 'TEST QUOTE DB 2!', 'TEST QUOTE FILE 1!'],
            ]
        ];
    }
}