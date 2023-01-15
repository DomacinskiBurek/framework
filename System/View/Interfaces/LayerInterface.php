<?php

namespace DomacinskiBurek\System\View\Interfaces;

interface LayerInterface
{
    public function createSharedLayer (string $template, $callback) : string;
}