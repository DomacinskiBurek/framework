<?php

namespace DomacinskiBurek\System\Cache;

use DomacinskiBurek\System\Cache\Interfaces\Cache;
use ParseError;
use Redis;
use RedisException;
use TypeError;

class RedisCache implements Cache
{
    private Redis $cache;

    public function connect(string $host, string $username, string $password, int $port)
    {
        $this->cache = new Redis([
            'host'           => $host,
            'port'           => $port,
            'connectTimeout' => 2.5,
            'auth'    => [$username, $password],
            'ssl'     => ['verify_peer' => false],
            'backoff' => [
                'algorithm' => Redis::BACKOFF_ALGORITHM_DECORRELATED_JITTER,
                'base'      => 500,
                'cap'       => 750,
            ]
        ]);
    }

    public function setCache(string $cacheKey, string $cacheValue, int $cacheExpire): void
    {
        try {
            $this->cache->set($cacheKey, $cacheValue, $cacheExpire);
        } catch (RedisException $error) {
            throw new ParseError($error->getMessage());
        }
    }

    public function getCache(string $cacheKey)
    {
        try {
            return $this->cache->get($cacheKey);
        } catch (RedisException $error) {
            throw new TypeError($error->getMessage());
        }
    }

    public function putCache(string $cacheKey, string $cacheValue, int $cacheExpire): void
    {
        $this->deleteCache($cacheKey);
        $this->setCache($cacheKey, $cacheValue, $cacheExpire);
    }

    public function deleteCache(string $cacheKey)
    {
        try {
            $this->cache->del($cacheKey);
        } catch (RedisException $error) {
            throw new TypeError($error->getMessage());
        }
    }
}