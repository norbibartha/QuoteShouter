<?php

namespace App\Services;

use App\Database\DatabaseClient;

class DatabaseService
{
    private const TABLE_QUOTES = 'quotes';
    private const TABLE_AUTHORS = 'authors';

    public function __construct(private DatabaseClient $databaseClient)
    {
    }

    /**
     * Get quotes from database
     *
     * Because of it is almost impossible to rebuild some names we use case-insensitive search in database
     *  Ex. Leonardo da Vinci
     *
     * @param string $authorName
     * @param int $limit
     *
     * @return array
     */
    public function getQuotes(string $authorName, int $limit = 10): array
    {
        $joinData = [
            $this->constructJoinData(
                tableName: self::TABLE_AUTHORS,
                joinCriteriaFields: [self::TABLE_QUOTES . '.author_id', self::TABLE_AUTHORS . '.id'],
                filters: [
                    'name' => [
                        'operator' => $this->databaseClient::OPERATOR_EQUALS,
                        'value' => $authorName,
                        'caseInsensitive' => true,
                    ]
                ],
            )];

        $dbQuotes = $this->databaseClient->select(
            fields: ['text'],
            table: self::TABLE_QUOTES,
            orderBy: [self::TABLE_QUOTES . '.text'],
            limit: $limit,
            joins: $joinData
        );

        // Normalize array
        return array_map(function (array $quoteData) {
            return $quoteData['text'];
        }, $dbQuotes);
    }

    /**
     * Use this function to make sure the structure is correct
     *
     * @param string $tableName
     * @param array $fields
     * @param array $joinCriteriaFields
     * @param array $filters
     *
     * @return array
     */
    private function constructJoinData(
        string $tableName,
        array $fields = [],
        array $joinCriteriaFields = [],
        array $filters = []
    ): array {
        $joinFilters = [];
        foreach ($filters as $field => $filter) {
            $joinFilters[$field] = [
                'operator' => $filter['operator'],
                'value' => $filter['value'],
                'caseInsensitive' => $filter['caseInsensitive'] ?? false,
            ];
        }

        return [
            'selectFields' => $fields,
            'table' => $tableName,
            'criteria' => $joinCriteriaFields,
            'filters' => $joinFilters,
        ];
    }
}