<?php

namespace DomacinskiBurek\System\View\Interfaces;

interface RenderInterface
{
    public static function render ($source, ?array $resource = null, int $code = 200);
}