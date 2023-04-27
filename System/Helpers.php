<?php

use DomacinskiBurek\System\View;

if (!function_exists("view")) {
    function view(mixed $data, array $addon = [], int $httpCode = 200): array
    {
        return (new View)->render($data, $addon, $httpCode);
    }
}