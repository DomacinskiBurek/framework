<?php

namespace DomacinskiBurek\System\Cache\Interfaces;

interface Cache
{
    public function connect (string $host, string $username, string $password, int $port);
    public function setCache(string $cacheKey, string $cacheValue, int $cacheExpire): void;
    public function getCache(string $cacheKey);
    public function putCache(string $cacheKey, string $cacheValue, int $cacheExpire): void;
    public function deleteCache(string $cacheKey);
}