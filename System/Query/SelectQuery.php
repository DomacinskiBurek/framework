<?php

namespace DomacinskiBurek\System\Query;

use DomacinskiBurek\System\Config;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;
use Exception;


class SelectQuery implements QueryInterface
{
    private array $queryList;

    /**
     * @throws Exception
     */
    public function __construct ()
    {
        $config = new Config();
        $config->load("select_query", "yaml");
        $this->queryList = $config->getAll("select_query");
    }

    /**
     * @throws Exception
     */
    public function get(string $query) : string
    {
        if (array_key_exists($query, $this->queryList) === false) throw new Exception('Query does not exist in our system.');

        return $this->queryList[$query];
    }
}