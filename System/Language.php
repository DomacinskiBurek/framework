<?php

namespace DomacinskiBurek\System;

use Exception;

class Language
{
    private static string $languageLocale = 'en_US';
    private string $location;

    private static ?Language $instance = null;

    protected function __construct() { }
    protected function __clone() { }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    private array $language           = [];
    private array $languageFiles      = [];

    public function setLanguageLocale (string $languageLocale): void
    {
        self::$languageLocale = $languageLocale;
    }

    public static function getLanguageLocale (): string
    {
        return self::$languageLocale;
    }

    public function getTranslation (string $langString): array
    {
        $targetFile = substr($langString, 0, strpos($langString, '.'));
        $targetLine = substr($langString, strlen($targetFile) + 1);

        if (!isset($this->language[$this->getLanguageLocale()][$targetFile]) || !array_key_exists($targetLine, $this->language[$this->getLanguageLocale()][$targetFile])) {
            $this->loadTranslation($targetFile, $this->getLanguageLocale());
        }

        return [$targetFile, $targetLine];
    }

    public function getLine (string $langString, array $resources = [], ?string $locale = null)
    {
        if (!is_null($locale)) $this->setLanguageLocale($locale);

        [$file, $line] = $this->getTranslation($langString);

        if (!array_key_exists($line, $this->language[$this->getLanguageLocale()][$file])) return $line;

        return !empty($resources) ? $this->formatTranslateLine($this->language[$this->getLanguageLocale()][$file][$line], $resources) : $this->language[$this->getLanguageLocale()][$file][$line];
    }

    protected function formatTranslateLine (string $line, array $resources): string
    {
        return sprintf($line, ...$resources);
    }

    protected function loadTranslation (string $targetFile, string $languageLocale): array
    {
        if (!array_key_exists($languageLocale, $this->languageFiles)) $this->languageFiles[$languageLocale] = [];
        if (in_array($targetFile, $this->languageFiles, true)) return [];

        if (!array_key_exists($languageLocale, $this->language)) $this->language[$languageLocale] = [];
        if (!array_key_exists($targetFile, $this->language[$languageLocale])) $this->language[$languageLocale][$targetFile] = [];

        $languagePath    = "$this->location/" . $this->getLanguageLocale() . "/$targetFile.php";

        $requireLanguage = $this->requireLanguage($languagePath);

        $this->languageFiles[$languageLocale][] = $targetFile;

        $this->language[$languageLocale][$targetFile] = $requireLanguage;

        return [];
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

    public static function Init(): Language
    {
        if (is_null(self::$instance)) self::$instance = new static();

        self::$instance->location = System::getLanguageDirectory();

        return self::$instance;
    }
}