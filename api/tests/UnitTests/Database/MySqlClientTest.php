<?php

namespace UnitTests\Database;

use App\Database\MySqlClient;
use Exception;
use PDOStatement;
use ReflectionException;
use UnitTests\BaseTestCase;

class MySqlClientTest extends BaseTestCase
{
    private MySqlClient $client;

    protected function setUp(): void
    {
        // Disable original constructor as we use it as Singleton
        $this->client = $this->createPartialMock(MySqlClient::class, []);
    }

    /**
     * @dataProvider constructSelectQueryDataProvider
     *
     * @param array $fields
     * @param string $table
     * @param array $filters
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param array $joins
     * @param string $expectedResult
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConstructSelectQuery(
        array $fields,
        string $table,
        array $filters,
        array $orderBy,
        int $limit = null,
        int $offset = null,
        array $joins,
        string $expectedResult,
    ): void {
        $result = $this->invokeMethod(
            $this->client,
            'constructSelectQuery',
            [$fields, $table, $filters, $orderBy, $limit, $offset, $joins]
        );
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function constructSelectQueryDataProvider(): array
    {
        return [
            [
                'fields' => ['field-1'],
                'table' => 'test-table',
                'filters' => [],
                'orderBy' => ['test-2-table.field-2'],
                'limit' => 5,
                'offset' => 0,
                'joins' => [
                    [
                        'selectFields' => [],
                        'table' => 'test-2-table',
                        'criteria' => [
                            'test-table.field-test',
                            'test-2-table.test-field'
                        ],
                        'filters' => [
                            'field-3' => [
                                'operator' => 'Equals',
                                'value' => 'test-value',
                                'caseInsensitive' => true,
                            ]
                        ],
                    ]
                ],
                'expectedResult' => 'SELECT test-table.field-1 FROM test-table INNER JOIN test-2-table ON ' .
                    'test-table.field-test = test-2-table.test-field WHERE lower(test-2-table.field-3) = :field-3 ' .
                    'ORDER BY test-2-table.field-2 LIMIT 5 OFFSET 0;',
            ]
        ];
    }

    /**
     * @dataProvider getFieldsWithTableNamesDataProvider
     *
     * @param array $fields
     * @param string $table
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testGetFieldsWithTableNames(array $fields, string $table, array $expectedResults): void
    {
        $results = $this->invokeMethod($this->client, 'getFieldsWithTableNames', [$fields, $table]);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function getFieldsWithTableNamesDataProvider(): array
    {
        return [
            [
                'fields' => ['field1', 'field2', 'field3'],
                'table' => 'test-table',
                'expectedResults' => ['test-table.field1', 'test-table.field2', 'test-table.field3'],
            ]
        ];
    }

    /**
     * @dataProvider constructWhereStatementsDataProvider
     *
     * @param array $filters
     * @param string $tableName
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConstructWhereStatements(array $filters, string $tableName, array $expectedResults): void
    {
        $results = $this->invokeMethod($this->client, 'constructWhereStatements', [$filters, $tableName]);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function constructWhereStatementsDataProvider(): array
    {
        return [
            'Empty set' => [
                'filters' => [],
                'tableName' => 'table1',
                'expectedResult' => [],
            ],
            'With one filter' => [
                'filters' => [
                    'test-field' => ['operator' => 'Equals', 'value' => 'test-name', 'caseInsensitive' => true]
                ],
                'tableName' => 'testTable',
                'expectedResult' => ['lower(testTable.test-field) = :test-field'],
            ]
        ];
    }

    /**
     * @dataProvider constructWhereStatementsExceptionDataProvider
     *
     * @param array $filters
     * @param string $tableName
     * @param string $exception
     * @param string $exceptionMessage
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConstructWhereStatementsException(
        array $filters,
        string $tableName,
        string $exception,
        string $exceptionMessage
    ): void {
        $this->expectException($exception);
        $this->expectExceptionMessage($exceptionMessage);
        $this->invokeMethod($this->client, 'constructWhereStatements', [$filters, $tableName]);
    }

    /**
     * @return array
     */
    public function constructWhereStatementsExceptionDataProvider(): array
    {
        return [
            [
                'filters' => [
                    'test-field' => ['operator' => 'not supported', 'value' => 'test-name', 'caseInsensitive' => true]
                ],
                'tableName' => 'test-table',
                'exception' => Exception::class,
                'exceptionMessage' => 'Specified operator does not exist!',
            ],
        ];
    }
}