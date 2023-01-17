<?php

namespace App\Services;

class FileProcessor
{
    /**
     * Get quotes from json file
     *
     * @param string $authorName
     * @param int $limit
     *
     * @return array
     */
    public function getQuotes(string $authorName, int $limit = 10): array
    {
        $path = getenv('QUOTES_FILE_PATH');
        $json = file_get_contents($path);
        $quotesData = json_decode($json, true);

        $quotes = [];
        foreach ($quotesData['quotes'] ?? [] as $data) {
            if (count($quotes) >= $limit) {
                break;
            }

            if (strtolower($data['author']) === $authorName) {
                $quotes[] = $data['quote'];
            }
        }

        return $quotes;
    }
}