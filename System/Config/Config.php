<?php

namespace DomacinskiBurek\System\Config;

class Config
{
    private static array $config = [];

    public static function get (string $property, string $config): ?string
    {
        if (!array_key_exists($config, self::$config)) return null;
        if (!array_key_exists($property, self::$config[$config])) return null;

        return self::$config[$config][$property];
    }

    public static function includeConfig (string $config): bool
    {
        $close = function ($handle, $bool) { closedir($handle); return $bool; };
        if (array_key_exists($config, self::$config)) return true;

        $handle = opendir(directoryConfig());
        while ($handle && false !== ($file = readdir($handle))) {
            if (in_array($file, ['.', '..'])) continue;

            preg_match("@$config.(?:yaml|yml|json|env)@", $file, $match);
            if (!empty($match)) {
                $filePath = implode(directorySeparator(), [directoryConfig(), $file]);
                $fileExtension = substr($file, mb_strlen($config) + 1);

                switch($fileExtension) {
                    case "yaml":
                    case "yml":
                        return self::yamlAdapter($config, $filePath);
                    case "json":
                        return self::jsonAdapter($config, $filePath);
                }

                return $close($handle, true);
            }
        }

        return $close($handle, false);
    }

    private static function yamlAdapter (string $config, string $filePath): bool
    {
        if (false !== ($data = yaml_parse_file($filePath))) {
            if (array_key_exists($config, self::$config)) self::$config[$config] = [];
            return self::collectConfigData($data, $config);
        }

        return false;
    }

    private static function jsonAdapter (string $config, string $filePath): bool
    {
        if (false !== ($data = json_decode($filePath, true))) {
            if (array_key_exists($config, self::$config)) self::$config[$config] = [];
            return self::collectConfigData($data, $config);
        }

        return false;
    }

    private static function collectConfigData (array $data, string $config): bool
    {
        $depth = 1;
        $moonWalk = function (array $data, ?string $stackKey = null) use ($config, &$depth, &$moonWalk)
        {
            array_walk($data, function ($configValue, $configKey, $stackKey) use (&$depth, &$moonWalk, $config) {
                $stackKey = (is_null($stackKey)) ? $configKey : sprintf("%s.%s", $stackKey, $configKey);

                if (is_array($configValue) && ++$depth <= 15) { // This is not counting levels proper way. Each time iteration gets new Array, it will count. Just prevention of endless loop
                    $moonWalk($configValue, $stackKey);
                } else {
                    self::$config[$config][$stackKey] = $configValue;
                }

            }, $stackKey);
        };

        $moonWalk($data);
        return true;
    }
}