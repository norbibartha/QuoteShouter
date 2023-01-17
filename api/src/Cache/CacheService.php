<?php

namespace App\Cache;

class CacheService
{
    /**
     * Generate key for cache
     *
     * @param array $keyItems
     *
     * @return string
     */
    protected function generateCachingKey(array $keyItems): string
    {
        return md5(json_encode($keyItems));
    }
}