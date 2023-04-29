<?php

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\View\Page;
use DomacinskiBurek\System\View\Template;

class View
{
    public function render(Request $request, mixed $data, array $addon, int $httpCode = 200): string
    {
        $request->code($httpCode);
        $request->header($this->header($data));

        return $this->pageParser($data, $addon);
    }

    private function pageParser (mixed $data, array $addon) : string
    {
        switch (true) {
            case is_array($data) || is_object($data):
                return json_encode($data);
            default:
                return (new Template(new Page()))->render($data, $addon);
        }
    }
    private function header (mixed $data): string
    {
        switch (true) {
            case is_array($data) || is_object($data):
                return "application/json";
            case $data === strip_tags($data):
                return "text/plain";
            default:
                return "text/html";
        }
    }
}