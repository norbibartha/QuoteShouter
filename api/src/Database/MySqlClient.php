<?php

namespace App\Database;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class MySqlClient implements DatabaseClient
{
    private const INVALID_OPERATOR_ERROR_MESSAGE = 'Specified operator does not exist!';
    private const VALID_OPERATORS = [
        self::OPERATOR_EQUALS => '=',
        self::OPERATOR_GREATER => '>',
        self::OPERATOR_GREATER_OR_EQUALS => '>=',
        self::OPERATOR_LESS => '<',
        self::OPERATOR_LESS_OR_EQUALS => '<=',
        self::OPERATOR_NOT_EQUALS => '<>',
        self::OPERATOR_IS_NULL => 'IS NULL',
        self::OPERATOR_IS_NOT_NULL => 'IS NOT NULL',
        self::OPERATOR_PATTERN => 'LIKE',
    ];

    private static MySqlClient $client;

    private function __construct(private PDO $connection)
    {
    }

    /**
     * @param string $host
     * @param string $dbName
     * @param string $user
     * @param string $password
     *
     * @return self
     *
     * @throws Exception
     */
    public static function getInstance(
        string $host,
        string $dbName,
        string $user,
        string $password
    ): self {
        if (!empty(self::$client)) {
            return self::$client;
        }

        try {
            $connectionString = sprintf('mysql:host=%s;dbname=%s;charset=UTF8', $host, $dbName);
            $connection = new PDO(
                $connectionString,
                $user,
                $password,
                [
                    // set the PDO error mode to exception
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // set the resulting array to associative
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            throw new Exception('Connection failed: ' . $exception->getMessage());
        }

        self::$client = new self($connection);

        return self::$client;
    }

    /**
     * @param array $fields
     * @param string $table
     * @param array $filters
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param array $joins
     *
     * @return bool|array
     *
     * @throws Exception
     */
    public function select(
        array $fields,
        string $table,
        array $filters = [],
        array $orderBy = [],
        int $limit = null,
        int $offset = null,
        array $joins = [],
    ): bool|array {
        try {
            $query = $this->constructSelectQuery(
                fields: $fields,
                table: $table,
                filters: $filters,
                orderBy: $orderBy,
                limit: $limit,
                offset: $offset,
                joins: $joins
            );

            // Merge all filters together
            foreach ($joins as $joinData) {
                $filters = array_merge($filters, $joinData['filters'] ?? []);
            }

            $statement = $this->connection->prepare($query);
            $statement = $this->bindParameters($filters, $statement);
            // Execute query
            $statement->execute();

            // Fetching rows into array
            return $statement->fetchAll();
        } catch (PDOException $exception) {
            throw new Exception('Error: ' . $exception->getMessage());
        }
    }

    /**
     * @param array $filters
     * @param PDOStatement $statement
     *
     * @return PDOStatement
     */
    private function bindParameters(array $filters, PDOStatement $statement): PDOStatement
    {
        foreach ($filters as $field => $expression) {
            $parameter = ':' . $field;
            $value = $expression['value'];

            if (is_string($value)) {
                // In case of case-insensitive search we compare lowercase strings
                if (!empty($expression['caseInsensitive'])) {
                    $value = strtolower($value);
                }

                // Use htmlspecialchars() to avoid XSS attack
                $value = htmlspecialchars($value);
            }

            // Use type parameter in order to filter out the possible bugs
            if (is_null($value)) {
                $statement->bindParam($parameter, $value, PDO::PARAM_NULL);
            } else if (is_int($value)) {
                $statement->bindParam($parameter, $value, PDO::PARAM_INT);
            } else if (is_bool($value)) {
                $statement->bindParam($parameter, $value, PDO::PARAM_BOOL);
            } else {
                $statement->bindParam($parameter, $value);
            }
        }

        return $statement;
    }

    /**
     * Build SQL query
     *
     * @param array $fields
     * @param string $table
     * @param array $filters
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * Structure example:
     * [
     *  [
     *      'selectFields' => ['author_id'],
     *      'table' => 'authors,
     *      'criteria' => ['author_id', 'id'],
     *      'filters' => [
     *          'name' => [
     *              'operator' => '=',
     *              'value' => 'steve jobs',
     *          ],
     *          ...
     *      ],
     *  ],
     *  ...
     * ]
     * @param array $joins
     *
     * @return string
     *
     * @throws Exception
     */
    private function constructSelectQuery(
        array $fields,
        string $table,
        array $filters = [],
        array $orderBy = [],
        int $limit = null,
        int $offset = null,
        array $joins = [],
    ): string {
        $fields = $this->getFieldsWithTableNames(fields: $fields, table: $table);

        $query = sprintf('SELECT %s FROM %s', implode(', ', $fields), $table);

        $whereStatements = $this->constructWhereStatements(filters: $filters, tableName: $table);

        foreach ($joins as $joinData) {
            $selectFields = $this->getFieldsWithTableNames(
                fields: $joinData['selectFields'] ?? [],
                table: $joinData['table']
            );
            $fields = array_merge($fields, $selectFields);

            $query .= sprintf(
                ' INNER JOIN %s ON %s = %s',
                $joinData['table'],
                $joinData['criteria'][0],
                $joinData['criteria'][1]
            );

            $joinWhereStatements = $this->constructWhereStatements(
                filters: $joinData['filters'] ?? [],
                tableName: $joinData['table']
            );
            $whereStatements = array_merge($whereStatements, $joinWhereStatements);
        }

        if (!empty($whereStatements)) {
            $query .= ' WHERE ' . implode(' AND ', $whereStatements);
        }

        if ($orderBy !== null) {
            $query .= ' ORDER BY ' . implode(', ', $orderBy);
        }

        if ($limit !== null) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset !== null) {
            $query .= ' OFFSET ' . $offset;
        }

        return $query . ';';
    }

    /**
     * @param array $fields
     * @param string $table
     *
     * @return array
     */
    private function getFieldsWithTableNames(array $fields, string $table): array
    {
        return array_map(function (string $field) use ($table) {
            return $table . '.' . $field;
        }, $fields);
    }

    /**
     * @param array $filters
     * @param string $tableName
     *
     * @return array
     *
     * @throws Exception
     */
    private function constructWhereStatements(array $filters, string $tableName): array
    {
        $whereStatements = [];
        foreach ($filters as $field => $expression) {
            if (empty(self::VALID_OPERATORS[$expression['operator']])) {
                throw new Exception(self::INVALID_OPERATOR_ERROR_MESSAGE);
            }

            $operator = self::VALID_OPERATORS[$expression['operator']];

            $criteria = !empty($expression['caseInsensitive']) ? 'lower(%s) %s :%s' : '%s %s :%s';

            $fieldWithTableName = $tableName . '.' . $field;
            $whereStatements[] = sprintf($criteria, $fieldWithTableName, $operator, $field);
        }

        return $whereStatements;
    }
}