<?php

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\Error\Handlers\DatabaseNotFound;
use DomacinskiBurek\System\View\Interfaces\RenderInterface;
use DomacinskiBurek\System\View\Template;

class View implements RenderInterface
{
    /**
     * @throws DatabaseNotFound
     */
    public static function render($source, ?array $resource = null, int $code = 200, ?string $header = null)
    {
        http_response_code($code);

        switch (true) {
            case (is_null($header) && is_array($source)):
                header("Content-Type: application/json");
                break;
            case (is_null($header) && is_string($source)):
                header("Content-Type: text/html");
                break;
            default:
                header("Content-Type: $header");
                break;
        }

        if (!is_string($source)) return $source;

        $template = new Template();
        $render = $template->createPageTemplate();

        return $render->render($source, $resource);
    }
}