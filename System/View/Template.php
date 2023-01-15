<?php

namespace DomacinskiBurek\System\View;

use Exception;
use DomacinskiBurek\System\View\Interfaces\LayerInterface;
use DomacinskiBurek\System\View\Interfaces\PageInterface;
use DomacinskiBurek\System\View\Interfaces\TemplateInteface;

class Template implements TemplateInteface
{
    private function createShareLayer () : LayerInterface
    {
        return new Layer ();
    }

    /**
     * @throws Exception
     */
    public function createPageTemplate(): PageInterface
    {
        return new Page ($this->createShareLayer());
    }
}