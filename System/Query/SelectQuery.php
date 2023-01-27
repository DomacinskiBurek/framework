<?php

namespace DomacinskiBurek\System\Query;

use DomacinskiBurek\System\Query\Interfaces\QueryInterface;
use Exception;


class SelectQuery implements QueryInterface
{
    private array $queryBox = [
        "user_login_check" => "SELECT password FROM mt_user WHERE username = :username"
    ];

    /**
     * @throws Exception
     */
    public function get(string $query) : string
    {
        if (array_key_exists($query, $this->queryBox) === false) throw new Exception('Query does not exist in our system.');

        return $this->queryBox[$query];
    }
}