<?php

namespace UnitTests\Services;

use App\Cache\RabbitMqService;
use App\Cache\RedisService;
use App\Database\DatabaseClient;
use App\Services\DatabaseService;
use App\Services\FileProcessor;
use App\Services\QuotesService;
use ReflectionException;
use UnitTests\BaseTestCase;

class QuoteServiceTest extends BaseTestCase
{
    private QuotesService $quotesService;
    private DatabaseService $databaseService;
    private FileProcessor $fileProcessor;
    private RedisService $redisService;
    private RabbitMqService $rabbitMqService;

    protected function setUp(): void
    {
        $this->databaseService = $this->createMock(DatabaseService::class);
        $this->fileProcessor = $this->createMock(FileProcessor::class);
        $this->redisService = $this->createMock(RedisService::class);
        $this->rabbitMqService = $this->createMock(RabbitMqService::class);
        $this->quotesService = new QuotesService(
            $this->databaseService,
            $this->fileProcessor,
            $this->redisService,
            $this->rabbitMqService
        );
    }

    /**
     * @dataProvider getQuotesByAuthorDataProvider
     *
     * @param string $authorName
     * @param int $limit
     * @param array $dbQuotes
     * @param array $fileQuotes
     * @param array|null $redisResponse
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testGetQuotesByAuthor(
        string $authorName,
        int $limit,
        array $dbQuotes,
        array $fileQuotes,
        ?array $redisResponse,
        array $expectedResults
    ): void {
        $this->databaseService->method('getQuotes')->willReturn($dbQuotes);
        $this->fileProcessor->method('getQuotes')->willReturn($fileQuotes);
        $this->redisService->method('getResponse')->willReturn($redisResponse);
        $results = $this->invokeMethod($this->quotesService, 'getQuotesByAuthor', [$authorName, $limit]);

        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function getQuotesByAuthorDataProvider(): array
    {
        return [
            'Case with processed data' => [
                'authorName' => 'robert frost',
                'limit' => 3,
                'dbQuotes' => ['Test quote db 1.'],
                'fileQuotes' => ['Test quote file 1!', 'Test quote file 2!'],
                'redisResponse' => null,
                'expectedResults' => ['TEST QUOTE DB 1!', 'TEST QUOTE FILE 1!', 'TEST QUOTE FILE 2!'],
            ],
            'Case with cached data' => [
                'authorName' => 'robert frost',
                'limit' => 3,
                'dbQuotes' => [],
                'fileQuotes' => [],
                'redisResponse' => ['TEST QUOTE DB 1!', 'TEST QUOTE FILE 1!', 'TEST QUOTE FILE 2!'],
                'expectedResults' => ['TEST QUOTE DB 1!', 'TEST QUOTE FILE 1!', 'TEST QUOTE FILE 2!'],
            ]
        ];
    }

    /**
     * @dataProvider deslugifyDataProvider
     *
     * @param string $name
     * @param string $expectedResult
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testDeslugify(string $name, string $expectedResult): void
    {
        $result = $this->invokeMethod($this->quotesService, 'deslugify', [$name]);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function deslugifyDataProvider(): array
    {
        return [
            'Case with abbreviation' => [
                'name' => 'test-s-name',
                'expectedResult' => 'test s. name',
            ],
            'Simple case' => [
                'name' => 'name-test',
                'expectedResult' => 'name test',
            ]
        ];
    }
}