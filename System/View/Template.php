<?php

namespace DomacinskiBurek\System\View;

use Closure;

class Template
{
    private array $loadedList = [];
    private int $depthLevel = 0;
    private int $maxDepthLevel = 3;
    public function render(string $template, Closure $param): string
    {
        $this->depthLevel++; // Preventing infinite loop cage
        $layers = $this->collectLayers($template);
        if (!empty($layers)) {
            foreach ($layers as $layer) {
                if (array_key_exists($layer, $this->loadedList)) {
                    $layerTemplate ??= $this->loadedList[$layer];
                } else {
                    $layerTemplate ??= $param($this->parsePathParse($layer));
                    $this->loadedList[$layer] = $layerTemplate;
                }

                if ($this->depthLevel < $this->maxDepthLevel) {
                    $template = str_replace($layer, $this->render($layerTemplate, $param), $template);
                }
            }
        }

        return $template;
    }

    private function parsePathParse(string $layerSlug): string
    {
        return str_replace(["{{", "}}", " "], "", str_replace("_", ".", strtolower($layerSlug)));
    }
    private function collectLayers (string $renderSource) : array
    {
        preg_match_all('/{{\s[A-Z_]+\s}}/m', $renderSource, $matches);
        return (empty($matches)) ? [] : $matches[0];
    }
}