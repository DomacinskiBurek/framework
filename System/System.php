<?php

namespace DomacinskiBurek\System;

use Exception;
use DomacinskiBurek\System\Error\Handlers\DirectoryFailure;
use DomacinskiBurek\System\Filesystem\File;
use DomacinskiBurek\System\Filesystem\Storage;
use PDO;

class System
{
    private static ?string $directory = null;
    private static ?string $separator = DIRECTORY_SEPARATOR;

    public static function getDirectory (): ?string
    {
        self::$directory ?? self::$directory = dirname(__DIR__);

        return self::$directory;
    }

    public static function getSeparator (): ?string
    {
        return self::$separator;
    }

    public static function getLanguageDirectory (): string
    {
        return self::getDirectory() . "/Language";
    }
}