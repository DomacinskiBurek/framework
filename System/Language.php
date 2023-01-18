<?php

namespace DomacinskiBurek\System;

class Language
{
    private string $defaultLocale = "en_US";
    private array $languages = [];
    private array $files = [];

    public function setLocale (?string $locale) : void
    {
        if (!is_null($locale)) $this->defaultLocale = $locale;
    }

    public function getLocale () : string
    {
        return $this->defaultLocale;
    }

    public function translate (string $langString, array $implement = [], ?string $locale = null)
    {
        if (!is_null($locale)) $this->setLocale($locale);

        [$file, $line] = $this->getTranslation($langString);

        $locale = $this->getLocale();
        if (!array_key_exists($line, $this->languages[$locale][$file])) return $line;

        return !empty($implement) ? $this->formatTranslateLine($this->languages[$locale][$file][$line], $implement) : $this->languages[$locale][$file][$line];
    }

    private function getTranslation (string $langString): array
    {
        $targetFile = substr($langString, 0, strpos($langString, '.'));
        $targetLine = substr($langString, strlen($targetFile) + 1);

        $locale = $this->getLocale();
        if (!isset($this->language[$locale][$targetFile]) || !array_key_exists($targetLine, $this->language[$locale][$targetFile])) {
            $this->loadTranslation($targetFile, $this->getLocale());
        }

        return [$targetFile, $targetLine];
    }

    private function formatTranslateLine (string $line, array $resources): string
    {
        return sprintf($line, ...$resources);
    }

    private function loadTranslation (string $targetFile, string $languageLocale): void
    {
        if (!array_key_exists($languageLocale, $this->files)) $this->files[$languageLocale] = [];
        if (in_array($targetFile, $this->files, true)) return;

        if (!array_key_exists($languageLocale, $this->languages)) $this->languages[$languageLocale] = [];
        if (!array_key_exists($targetFile, $this->languages[$languageLocale])) $this->languages[$languageLocale][$targetFile] = [];

        $languagePath    = System::getLanguageDirectory() . "/" . $this->getLocale() . "/$targetFile.php";

        $requireLanguage = $this->requireLanguage($languagePath);

        $this->files[$languageLocale][] = $targetFile;

        $this->languages[$languageLocale][$targetFile] = $requireLanguage;
    }

    private function requireLanguage (string $languagePath) : array
    {
        if (!is_file($languagePath)) return [];

        $list = require $languagePath;

        if (isset($list[1])) {
            $list = array_replace_recursive(...$list);
        } elseif (isset($list[0])) {
            $list = $list[0];
        }

        return (!is_array($list)) ? [] : $list;
    }
}