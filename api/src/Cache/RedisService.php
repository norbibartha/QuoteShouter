<?php

namespace App\Cache;

use Predis\Client;

class RedisService extends CacheService
{
    public function __construct(private Client $redisClient)
    {
    }

    /**
     * Check if response is already cached
     *
     * @param array $keyItems
     *
     * @return array|null
     */
    public function getResponse(array $keyItems): ?array
    {
        $cachingKey = $this->generateCachingKey(keyItems: $keyItems);

        $response = $this->redisClient->get($cachingKey);

        if ($response !== null) {
            $response = $this->normalizeResponse($response);
        }

        return $response;
    }

    /**
     * @param string $response
     *
     * @return array
     */
    private function normalizeResponse(string $response): array
    {
        return json_decode($response, true);
    }
}