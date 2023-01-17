<?php

namespace App\Database;

interface DatabaseClient
{
    public const OPERATOR_EQUALS = 'Equals';
    public const OPERATOR_GREATER = 'Greater';
    public const OPERATOR_GREATER_OR_EQUALS = 'Greater or equals';
    public const OPERATOR_LESS = 'Less';
    public const OPERATOR_LESS_OR_EQUALS = 'Less or equals';
    public const OPERATOR_NOT_EQUALS = 'Not equals';
    public const OPERATOR_IS_NULL = 'Is null';
    public const OPERATOR_IS_NOT_NULL = 'Is not null';
    public const OPERATOR_PATTERN = 'Pattern';

    public function select(
        array $fields,
        string $table,
        array $filters = [],
        array $orderBy = [],
        int $limit = null,
        int $offset = null,
        array $joins = [],
    );
}