<?php

namespace DomacinskiBurek\System;

use Dotenv\Dotenv;
use DomacinskiBurek\System\Filesystem\File;
use TypeError;

class Config
{
    private array $config = [];

    public function load (string $config_name, string $config_type): void
    {
        if (!$this->has($config_name)) {
            switch ($config_type) {
                case "yaml":
                    $this->yamlConfig($config_name);
                    break;
                case "env":
                    $this->envConfig($config_name);
                    break;
                default:
                    throw new TypeError("unsupported config type");
            }
        }
    }

    public function get (string $config_string, ?string $param = null)
    {
        if (!isset($this->config[$config_string])) return null;
        if (!is_null($param) && !isset($this->config[$config_string][$param])) return null;


        return (!is_null($param)) ? $this->config[$config_string][$param] : $this->config[$config_string];
    }

    private function has (string $config_string) : bool
    {
        return $this->config[$config_string] ?? false;
    }

    private function envConfig (string $config_name): void
    {
        $content = Dotenv::createArrayBacked(System::getDirectory(), ".$config_name")->load();

        if (!is_null($content)) $this->config[$config_name] = $content;
    }
    private function yamlConfig (string $config_name): void
    {
        $location = System::getDirectory();

        switch (true) {
            case (file_exists("$location/$config_name.yml")):
                $file ??= "$location/$config_name.yml";
                break;
            case (file_exists("$location/$config_name.yaml")):
                $file ??= "$location/$config_name.yaml";
                break;
            default:
                throw new TypeError("unsupported yaml type");
        }

        $content = yaml_parse_file($file);

        if ($content !== false) $this->config[$config_name] = $content;
    }
}