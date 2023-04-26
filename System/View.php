<?php

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\View\Page;
use DomacinskiBurek\System\View\Template;

class View
{
    public function render(mixed $data, array $addon, int $httpCode = 200): array
    {
        return ["content" => $this->pageParser($data, $addon), "type" => sprintf("content-type: %s", $this->header($data)), "code" => $httpCode];
    }

    private function pageParser (mixed $data, array $addon) : string
    {
        switch (true) {
            case is_array($data) || is_object($data):
                return json_encode($data);
            default:
                return (new Page(new Template()))->render($data, $addon);
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