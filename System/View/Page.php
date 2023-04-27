<?php

namespace DomacinskiBurek\System\View;

use Closure;

class Page
{
    private int $depthLevel = 1;
    private int $maxDepthLevel = 3;
    private array $loadedTags = [];

    public function build(string $template, Closure $param): string
    {
        $tags = $this->getTemplateTags($template);
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                if (array_key_exists($tag, $this->loadedTags)) {
                    $tagTemplate = $this->loadedTags[$tag];
                } else {
                    $tagTemplate = $param($this->tagPath($tag));
                    $this->loadedTags[$tag] = $tagTemplate;
                }

                $template = substr_replace($template, $tagTemplate, strpos($template, $tag, 1), mb_strlen($tag));
            }
        }

        if (++$this->depthLevel < $this->maxDepthLevel) $template = $this->build($template, $param);
        return $template;
    }

    private function tagPath (string $tag): string
    {
        preg_match('/[A-Z_]+/m', $tag, $matches);
        return str_replace("_", ".", strtolower($matches[0] ?? ""));
    }

    private function getTemplateTags (string $template) : array
    {
        preg_match_all('/{{\s[A-Z_]+\s}}/m', $template, $matches);
        return (empty($matches)) ? [] : $matches[0];
    }
}