<?php

namespace UnitTests\Services;

use App\Services\FileProcessor;
use PHPUnit\Framework\TestCase;

class FileProcessorTest extends TestCase
{
    private FileProcessor $fileProcessor;

    protected function setUp(): void
    {
        $this->fileProcessor = new FileProcessor();
    }

    /**
     * @dataProvider getQuotesDataProvider
     *
     * @param string $authorName
     * @param int $limit
     * @param array $expectedResults
     *
     * @return void
     */
    public function testGetQuotesFromFile(string $authorName, int $limit, array $expectedResults): void
    {
        $results = $this->fileProcessor->getQuotes($authorName, $limit);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function getQuotesDataProvider(): array
    {
        return [
            [
                'authorName' => 'robert frost',
                'limit' => 2,
                'expectedResults' => ['Test quote file 2.', 'Test quote file 1.'],
            ]
        ];
    }
}