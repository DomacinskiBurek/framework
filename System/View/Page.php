<?php

namespace DomacinskiBurek\System\View;

use DomacinskiBurek\System\Error\Handlers\FileNotFound;
use DomacinskiBurek\System\Error\Handlers\NotFound;

class Page extends PageFounder
{
    /**
     * @throws NotFound
     * @throws FileNotFound
     */
    public function render(string $source, ?array $resource = null): string
    {
        if (is_null($resource) === false) $this->resource = array_merge($resource, $this->resource);

        return $this->layer->createSharedLayer($this->renderSource($source), function (string $source) {
            return $this->renderSource($source);
        });
    }

    /**
     * @throws NotFound
     */
    protected function renderSource (string $source) : string
    {
        $sourceParse = $this->renderPathParse($source);

        if (!is_file($sourceParse)) throw new NotFound('Unable to load view.');

        extract($this->resource);

        ob_start();
        include $sourceParse;

        return ob_get_clean() ?: '';
    }

    protected function renderPathParse (string $source): string
    {
        $rootDir = $this->directory;

        $view_route = array_merge(explode('/', $rootDir), ['Application'], explode('.', $source));

        $build_file = end($view_route) . '.Render.php';

        unset($view_route[array_key_last($view_route)]);

        $view_route = array_merge($view_route, ['View', $build_file]);

        return implode('/', $view_route);
    }
}