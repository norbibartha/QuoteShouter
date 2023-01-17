<?php

namespace App\Database;

use Exception;

class MySqlClientFactory
{
    /**
     * Create MySqlClient instance
     *
     * @throws Exception
     */
    public static function getClient(): DatabaseClient
    {
        return MySqlClient::getInstance(
            host: getenv('MYSQL_HOST'),
            dbName: getenv('MYSQL_DATABASE'),
            user: getenv('MYSQL_USER'),
            password: getenv('MYSQL_PASSWORD'),
        );
    }
}