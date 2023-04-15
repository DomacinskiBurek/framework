<?php

namespace DomacinskiBurek\System\View;

use DomacinskiBurek\System\Error\Handlers\FileNotFound;
use DomacinskiBurek\System\Error\Handlers\NotFound;
use DomacinskiBurek\System\View\Interfaces\LayerInterface;

class Layer implements LayerInterface
{
    protected array $registerLayers = [
        "{{ USER_SIDEBAR }}" => "Shared.Sidebar",
        "{{ AUTH_HEADER }}"  => "Shared.authHeader",
        "{{ AUTH_FOOTER }}"  => "Shared.authFooter"
    ];

    /**
     * @throws NotFound
     * @throws FileNotFound
     */
    public function createSharedLayer(string $template, $callback): string
    {
        foreach (array_keys($this->registerLayers) as $layer) if (str_contains($template, $layer) === true) $template = str_replace($layer, $callback($this->registerLayers[$layer]), $template);

        return $template;
    }
}