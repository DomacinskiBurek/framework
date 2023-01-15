<?php

namespace DomacinskiBurek\System\Query\Interfaces;

interface QueryInterface
{
    public function build (string $query, ?array $params): string;
}
