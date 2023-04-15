<?php

namespace DomacinskiBurek\System\Cache;

use DomacinskiBurek\System\Cache\Interfaces\Cache;

class MemCache implements Cache
{
    public function connect(string $host, string $username, string $password, int $port)
    {
        // TODO: Implement connect() method.
    }

    public function setCache(string $cacheKey, string $cacheValue, int $cacheExpire): void
    {
        // TODO: Implement setCache() method.
    }

    public function getCache(string $cacheKey)
    {
        // TODO: Implement getCache() method.
    }

    public function putCache(string $cacheKey, string $cacheValue, int $cacheExpire): void
    {
        // TODO: Implement putCache() method.
    }

    public function deleteCache(string $cacheKey)
    {
        // TODO: Implement deleteCache() method.
    }
}