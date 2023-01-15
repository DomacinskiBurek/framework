<?php

namespace DomacinskiBurek\System;

use Exception;
use PDO, PDOException;
use DomacinskiBurek\System\Error\Handlers\DatabaseException;
use DomacinskiBurek\System\Error\Handlers\DatabaseStringUnknown;
use TypeError;

/**
 * Reference to Refactoring Guru.
 */
class Database
{
    private static array $instances = [];

    protected function __construct () {}
    protected function __clone () {}

    /**
     * @throws Exception
     */
    public function __wakeup(): void
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * @throws DatabaseException
     */
    public static function connect (string $dbConfig) : ?PDO
    {
        if (!isset(self::$instances[$dbConfig])) {
            self::$instances[$dbConfig] = self::connection($dbConfig);
        }

        return self::$instances[$dbConfig];
    }

    /**
     * @throws DatabaseException
     */
    private static function connection (string $dbConfig): PDO
    {
        $config = new Config ();
        $config->load("config", "yaml");
        $config->load($dbConfig, "yaml");

        $object = $config->get($dbConfig, ($config->get("config", "PRODUCTION") === true ? "PRODUCTION" : "DEVELOPMENT"));

        try {
            $object   = self::connectionString($object);
            $instance = new PDO($object->query, $object->username, $object->password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_general_ci'"]);
        } catch (PDOException $error) {
            throw new DatabaseException($error->getMessage());
        }

        return $instance;
    }

    private static function connectionString (array $params) : object
    {
        switch ($params["DRIVER"]) {
            case 'mysql':
                return (object) ["query" => sprintf("mysql:dbname=%s;host=%s", $params["DATABASE"], $params['HOST']), "username" => $params['USER'], "password" => $params['PASSWORD'], "charset" => $params['CHARSET']];
        }

        throw new TypeError('Database string could not be found!');
    }
}