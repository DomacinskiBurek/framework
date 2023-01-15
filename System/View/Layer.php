<?php

namespace DomacinskiBurek\System\View;

use DomacinskiBurek\System\Error\Handlers\FileNotFound;
use DomacinskiBurek\System\Error\Handlers\NotFound;
use DomacinskiBurek\System\View\Interfaces\LayerInterface;

class Layer implements LayerInterface
{
    protected array $registerLayers = [
        "{{ USER_SIDEBAR }}"        => "Shared.Sidebar",
        "{{ META_TAG_LIST }}"       => "Shared.MetaTags",
        "{{ MAIN_HEADER }}"         => "Shared.MainHeader",
        "{{ MAIN_FOOTER }}"         => "Shared.MainFooter",
        "{{ AUTH_FOOTER }}"         => "Shared.AuthFooter",
        "{{ PROFILE_SIMPLEBAR }}"   => "Admin.Profile.Simplebar",
        "{{ PORTAL_META }}"         => "Shared.PortalMeta",
        "{{ PORTAL_MAIN_NAV }}"     => "Shared.PortalMainNav",
        "{{ PORTAL_SIDE_NAV }}"     => "Shared.PortalSubNav",
        "{{ PORTAL_MAIN_FOOTER }}"  => "Shared.PortalMainFooter"
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