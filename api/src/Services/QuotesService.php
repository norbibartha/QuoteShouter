<?php

namespace App\Services;

use App\Cache\RabbitMqService;
use App\Cache\RedisService;
use Exception;

class QuotesService
{
    /**
     * @throws Exception
     */
    public function __construct(
        private DatabaseService $databaseService,
        private FileProcessor $fileProcessor,
        private RedisService $redisService,
        private RabbitMqService $rabbitMqService
    ) {
    }

    /**
     * @param string $authorName
     * @param int $limit
     *
     * @return array
     *
     * @throws Exception
     */
    public function getQuotesByAuthor(string $authorName, int $limit): array
    {
        // First check if the response is cached
        $responseIdentifier = ['authorName' => $authorName, 'limit' => $limit];
        $response = $this->redisService->getResponse(keyItems: $responseIdentifier);

        if ($response !== null) {
            return $response;
        }

        /*
         * Build up real name from slug
         *      Example: steve-jobs => steve jobs
         */
        $authorName = $this->deslugify(name: $authorName);

        $dbQuotes = $this->databaseService->getQuotes(authorName: $authorName, limit: $limit);

        $jsonQuotes = $this->fileProcessor->getQuotes(authorName: $authorName, limit: $limit);

        // Merge quotes from the two sources, then eliminate the duplicates
        $quotes = array_unique(array_merge($dbQuotes, $jsonQuotes));

        // Sort quotes so we keep only the first $limit items
        sort($quotes);

        /*
         * Keep the first $limit item, then transform quotes to shouts
         *  Example:
         *      Quote = If the wind will not serve, take to the oars.
         *      Shout = IF THE WIND WILL NOT SERVE, TAKE TO THE OARS!
         */
        $quotes = array_map(function (string $quote) {
            return str_replace('.', '!', strtoupper($quote));
        }, array_slice($quotes, 0, $limit));

        // Send response to RabbitMq, so next time we will have it in cache (Redis)
        $this->rabbitMqService->publish($responseIdentifier, $quotes);

        return $quotes;
    }

    /**
     * 1. Make sure the string is lowercase. Ex: steve-jobs
     * 2. Remove '-'. Ex: steve jobs
     * 3. Check if there is any abbreviation and add '.' to it. Ex: steve l. jobs
     *
     * @param string $name
     *
     * @return string
     */
    private function deslugify(string $name): string
    {
        $name = str_replace('-', ' ', strtolower($name));

        $nameArray = array_map(function (string $name) {
            if (strlen($name) === 1) {
                $name .= '.';
            }

            return $name;
        }, explode(' ', $name));

        return implode(' ', $nameArray);
    }
}