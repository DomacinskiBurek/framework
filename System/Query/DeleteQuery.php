<?php

namespace DomacinskiBurek\System\Query;

use DomacinskiBurek\System\Model;
use Exception;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;

class DeleteQuery implements QueryInterface
{

    private array $queryBox = [];

    /**
     * @throws Exception
     */
    public function get(string $query) : string
    {
        if (array_key_exists($query, $this->queryBox) === false) throw new Exception('Query does not exist in our system.');

        return $this->queryBox[$query];
    }
}