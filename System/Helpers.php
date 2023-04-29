<?php

use DomacinskiBurek\System\Config\Config;
use DomacinskiBurek\System\Request;
use DomacinskiBurek\System\View;

if (!function_exists("directoryLang")) {
    function directoryLang (): string
    {
        return implode(directorySeparator(), [directoryRoot(), "System", "Language"]);
    }
}

if (!function_exists("directoryRoot")) {
    function directoryRoot (): string
    {
        return dirname(__DIR__);
    }
}

if (!function_exists("directoryConfig")) {
    function directoryConfig(): string
    {
        return implode(directorySeparator(), [directoryRoot(), "System", "Config", "List"]);
    }
}

if (!function_exists("directorySeparator")) {
    function directorySeparator (): string
    {
        return DIRECTORY_SEPARATOR;
    }
}

if (!function_exists("view")) {
    function view(mixed $data, array $addon = [], int $httpCode = 200): string
    {
        return (new View)->render(new Request(), $data, $addon, $httpCode);
    }
}

if (!function_exists("isProduction")) {
    function isProduction (): bool
    {
        $value = Config::get("PRODUCTION", "config");
        return (!is_null($value) && $value == 1);
    }
}