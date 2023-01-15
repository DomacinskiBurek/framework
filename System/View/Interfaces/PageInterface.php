<?php

namespace DomacinskiBurek\System\View\Interfaces;

interface PageInterface
{
    public function render (string $source, ?array $resource = null);
}