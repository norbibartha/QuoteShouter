<?php

namespace UnitTests\Services;

use App\Database\DatabaseClient;
use App\Services\DatabaseService;
use ReflectionException;
use UnitTests\BaseTestCase;

class DatabaseServiceTest extends BaseTestCase
{
    private DatabaseService $databaseService;
    private DatabaseClient $databaseClient;

    protected function setUp(): void
    {
        $this->databaseClient = $this->createMock(DatabaseClient::class);
        $this->databaseService = new DatabaseService($this->databaseClient);
    }

    /**
     * @dataProvider getQuotesDataProvider
     *
     * @param string $authorName
     * @param int $limit
     * @param array $dbQuotes
     * @param array $expectedResults
     *
     * @return void
     */
    public function testGetQuotes(string $authorName, int $limit, array $dbQuotes, array $expectedResults): void
    {
        $this->databaseClient->method('select')->willReturn($dbQuotes);
        $results = $this->databaseService->getQuotes($authorName, $limit);

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
                'limit' => 1,
                'dbQuotes' => [['text' => 'Test quote db 1.']],
                'expectedResults' => ['Test quote db 1.'],
            ]
        ];
    }

    /**
     * @dataProvider constructJoinDataProvider
     *
     * @param string $tableName
     * @param array $fields
     * @param array $joinCriteriaFields
     * @param array $filters
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConstructJoinData(
        string $tableName,
        array $fields,
        array $joinCriteriaFields,
        array $filters,
        array $expectedResults,
    ): void {
        $results = $this->invokeMethod(
            $this->databaseService,
            'constructJoinData',
            [$tableName, $fields, $joinCriteriaFields, $filters]
        );

        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function constructJoinDataProvider(): array
    {
        return [
            [
                'tableName' => 'testTable',
                'fields' => ['field-1'],
                'joinCriteriaFields' => [
                    'testTable.field-3',
                    'anotherTable.field-2',
                ],
                'filters' => [
                    'field-2' => [
                        'operator' => 'Greater',
                        'value' => 'test-value',
                        'caseInsensitive' => true,
                    ]
                ],
                'expectedResults' => [
                    'selectFields' => ['field-1'],
                    'table' => 'testTable',
                    'criteria' => ['testTable.field-3', 'anotherTable.field-2'],
                    'filters' => [
                        'field-2' => [
                            'operator' => 'Greater',
                            'value' => 'test-value',
                            'caseInsensitive' => true,
                        ]
                    ],
                ],
            ]
        ];
    }
}