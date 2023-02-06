<?php

namespace DomacinskiBurek\System\Query;

use DomacinskiBurek\System\Config;
use Exception;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;

class DeleteQuery implements QueryInterface
{

    private array $queryList;

    /**
     * @throws Exception
     */
    public function __construct ()
    {
        $config = new Config();
        $config->load("delete_query", "yaml");
        $this->queryList = $config->getAll("delete_query");
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