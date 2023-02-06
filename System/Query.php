<?php

namespace DomacinskiBurek\System;

use Exception;
use DomacinskiBurek\System\Query\InsertQuery;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;
use DomacinskiBurek\System\Query\SelectQuery;
use DomacinskiBurek\System\Query\UpdateQuery;
use DomacinskiBurek\System\Query\DeleteQuery;
use TypeError;

class Query
{
    /**
     * @throws Exception
     */
    public static function generate (string $queryName, array|Model|null $queryParams = null): string
    {
        return static::build(static::determinate(static::queryType($queryName)), substr($queryName, (strpos($queryName, ':') + 2)), $queryParams);
    }

    /**
     * @throws Exception
     */
    private static function build(QueryInterface $query, string $queryString, array|null $params): string
    {
        switch (true) {
            case (is_null($params)):
                return $query->get($queryString);
            case (is_array($params)):
                return sprintf($query->get($queryString), ...$params);
            default:
                throw new TypeError("unexpected");
        }
    }

    /**
     * @throws Exception
     */
    private static function determinate (string $queryType): QueryInterface
    {
        switch ($queryType) {
            case 'Insert':
                return new InsertQuery();
            case 'Update':
                return new UpdateQuery();
            case 'Select':
                return new SelectQuery();
            case 'delete':
                return new DeleteQuery();
        }

        throw new Exception('Query Type not found.');
    }

    /**
     * @throws Exception
     */
    private static function queryType (string $query) : string
    {
        switch (true) {
            case (str_contains(strtolower($query), "insert")):
                return 'Insert';
            case (str_contains(strtolower($query), "update")):
                return 'Update';
            case (str_contains(strtolower($query), "select")):
                return 'Select';
            case (str_contains(strtolower($query), "delete")):
                return 'delete';
        }

        throw new Exception('Could not determinate right query');
    }
}