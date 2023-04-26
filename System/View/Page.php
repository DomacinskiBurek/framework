<?php

namespace DomacinskiBurek\System\View;

use DomacinskiBurek\System\Error\Handlers\NotFound;
use DomacinskiBurek\System\System;

class Page
{
    private string $directory;
    private Template $template;

    public function __construct (Template $template)
    {
        $this->template = $template;
        $this->directory = System::getSeparator() == '\\' ? str_replace(System::getSeparator(), '/', dirname(__DIR__, 2)) : dirname(__DIR__, 2);
    }
    public function render (string $source, array $addon): string
    {
        return $this->template->render($this->renderSource($source, $addon), function (string $source) use ($addon) {
            return $this->renderSource($source, $addon);
        });
    }

    protected function renderSource (string $source, array $addon) : string
    {
        $sourceParse = $this->renderPathParse($source);
        if (!is_file($sourceParse)) throw new \Exception('Unable to load view.');

        extract($addon);

        ob_start();
        include $sourceParse;

        return ob_get_clean() ?: '';
    }

    protected function renderPathParse (string $source): string
    {
        $rootDir = $this->directory;

        $view_route = array_merge(explode(System::getSeparator(), $rootDir), ['Application'], array_map(fn(string $string) => ucwords($string), explode('.', $source)));

        $build_file = strtolower(end($view_route)) . '.render.php';

        unset($view_route[array_key_last($view_route)]);

        $view_route = array_merge($view_route, ['View', $build_file]);

        return implode('/', $view_route);
    }
}