<?php

namespace DomacinskiBurek\System\View;

use Closure;
use DomacinskiBurek\System\System;
use Exception;

class Template
{
    private string $directory;
    public function __construct(private Page $page)
    {
        $this->directory = System::getSeparator() == '\\' ? str_replace(System::getSeparator(), '/', dirname(__DIR__, 2)) : dirname(__DIR__, 2);
    }

    public function render (string $source, array $addon): string
    {
        return $this->page->build(
            $this->templateLoad($source, $addon),
            fn(string $source) => $this->templateLoad($source, [], true)
        );
    }

    /**
     * @throws Exception
     */
    protected function templateLoad (string $source, array $addon = [], bool $subPage = false): string
    {
        $templatePath = $this->templatePath($source);
        if (!is_file($templatePath) || !is_readable($templatePath)) {
            if ($subPage) return "";

            throw new Exception("could not get page");
        }

        extract($addon);

        ob_start();
        include $templatePath;

        return ob_get_clean() ?: '';
    }

    protected function templatePath (string $source): string
    {
        $rootDir = $this->directory;

        $view_route = array_merge(explode(System::getSeparator(), $rootDir), ['Application'], array_map(fn(string $string) => ucwords($string), explode('.', $source)));

        $build_file = strtolower(end($view_route)) . '.render.php';

        unset($view_route[array_key_last($view_route)]);

        $view_route = array_merge($view_route, ['View', $build_file]);

        return implode('/', $view_route);
    }
}