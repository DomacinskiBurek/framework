<?php

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\Config\Config;
use DomacinskiBurek\System\Error\Handlers\DatabaseException;
use Exception;
use PDO;
use PDOException;
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
    public static function connect (string $dbConfig = "database") : ?PDO
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
        Config::includeConfig($dbConfig);

        try {
            $object   = self::connectionString($dbConfig);
            $instance = new PDO($object->query, $object->username, $object->password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_general_ci'"]);
        } catch (PDOException $error) {
            throw new DatabaseException($error->getMessage());
        }

        return $instance;
    }

    private static function connectionString (string $dbConfig) : object
    {
        $isProduction = (isProduction()) ? "PRODUCTION" : "DEVELOPMENT";

        $driver = Config::get("$isProduction.DRIVER", $dbConfig);
        $dbBase = Config::get("$isProduction.DATABASE", $dbConfig);
        $dbHost = Config::get("$isProduction.HOST", $dbConfig);
        $dbUser = Config::get("$isProduction.USER", $dbConfig);
        $dbPass = Config::get("$isProduction.PASSWORD", $dbConfig);
        $dbCharset = Config::get("$isProduction.CHARSET", $dbConfig);

        switch ($driver) {
            case 'mysql':
                return (object) ["query" => sprintf("mysql:dbname=%s;host=%s", $dbBase, $dbHost), "username" => $dbUser, "password" => $dbPass, "charset" => $dbCharset];
        }

        throw new TypeError('Database string could not be found!');
    }
}