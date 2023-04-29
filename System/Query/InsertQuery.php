<?php

namespace DomacinskiBurek\System\Query;

use DomacinskiBurek\System\Config\Config;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;
use Exception;

class InsertQuery implements QueryInterface
{
    private array $queryList;

    /**
     * @throws Exception
     */
    public function __construct ()
    {
        Config::includeConfig("insert_query");
    }

    /**
     * @throws Exception
     */
    public function get(string $query) : string
    {
        $rawQuery = Config::get($query, "insert_query");

        return (is_null($rawQuery)) ?
            throw new Exception('Query does not exist in our system.')
            : $rawQuery;
    }
}