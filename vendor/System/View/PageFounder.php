<?php

namespace DomacinskiBurek\System\View;

use Exception;
use DomacinskiBurek\System\Config;
use DomacinskiBurek\System\Error\Handlers\DatabaseNotFound;
use DomacinskiBurek\System\Language;
use DomacinskiBurek\System\System;
use DomacinskiBurek\System\View\Interfaces\PageInterface;

abstract class PageFounder implements PageInterface
{
    protected string $directory;
    protected array $resource = [];

    protected Layer $layer;

    /**
     * @throws Exception
     */
    public function __construct (Layer $layer)
    {
        $this->directory = System::getSeparator() == '\\' ? str_replace(System::getSeparator(), '/', dirname(__DIR__, 2)) : dirname(__DIR__, 2);
        $this->layer     = $layer;
    }
}