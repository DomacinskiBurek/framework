<?php

namespace DomacinskiBurek\System;

use Exception;
use DomacinskiBurek\System\Error\Handlers\DirectoryFailure;
use DomacinskiBurek\System\Filesystem\File;
use DomacinskiBurek\System\Filesystem\Storage;
use PDO;

class System
{
    private static ?string $directory = null;
    private static ?string $separator = DIRECTORY_SEPARATOR;
    private static array $languages   = [];

    public static function getDirectory (): ?string
    {
        self::$directory ?? self::$directory = dirname(__DIR__);

        return self::$directory;
    }

    public static function getSeparator (): ?string
    {
        return self::$separator;
    }

    public static function getLanguageDirectory (): string
    {
        return self::getDirectory() . "/Cache";
    }

    public static function getCacheDirectory (): string
    {
        return self::getDirectory() . "/Cache";
    }
    public static function getClientAddress()
    {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (self::validateClientAddress($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? false;
    }

    public static function validateClientAddress(string $ipAddress): bool
    {
        return !((filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false));
    }

    /**
     * @throws Exception
     */
    public static function getLanguageList (): array
    {
        if (!empty(self::$languages)) return self::$languages;

        $database = Database::connect('database');

        $prepare  = $database->prepare(Query::generate('Select::site_languages'));
        $prepare->execute();

        self::$languages = $prepare->fetchAll(PDO::FETCH_ASSOC);

        return self::$languages;
    }


    public static function getActiveLanguage (): string
    {
        $session = new Session();
        $locale  = Language::getLanguageLocale();

        if ($session->has('LanguageLocale')) {
            $locale = $session->get('LanguageLocale');
        } else if ($session->has('UserLogin')) {
            if ($session->has('LanguageLocale') === false) {
                $user   = userdetails();
                $locale = $user->locale;
                $session->set("LanguageLocale", $locale);
            }

            $locale ??= Language::getLanguageLocale();
        } else {
            $database = Database::connect("MaturantiBaDb");
            $prepare_sql = $database->prepare(Query::generate("Select::site_default_language"));
            $prepare_sql->execute();

            $fetch_default = $prepare_sql->fetch(PDO::FETCH_OBJ);
            if (!empty($fetch_default)) {
                $session->set("LanguageLocale", $fetch_default->locale);
                $locale = $fetch_default->locale;
            }
        }

        return $locale ?? "en_US";
    }

    public static function getFileTypeByExtension (string $extension): string
    {
        switch ($extension) {
            case "png":
                return "image/png";
            case "svg":
                return "image/svg+xml";
            case "jpg":
                return "image/jpg";
            case "jpeg":
            default:
                return "image/jpeg";
        }
    }

    /**
     * @throws DirectoryFailure
     */
    public static function uploadAttachment (array $property, string $filename, string $location): ?string
    {
        if ($property["size"] > 10737418240) return "filesize must be less than 10MB";

        if (!in_array(strtolower($property["extension"]), ["jpg", "png", "jpeg"])) return "file must contain valid extension";

        if (Storage::directoryGenerator($location) === false) return "unable to process your request";

        if (move_uploaded_file($property["tmp_location"], "$location/$filename.{$property["extension"]}") === false) return "unable to store your attachment";

        return null;
    }

    public static function resizeImage (File $file, ?int $newWidth)
    {
        $image = $file->fread($file->getSize());

        if (is_null($newWidth)) return $image;

        $image = imagecreatefromstring($image);

        $width  = imagesx($image);
        $height = imagesy($image);

        $ratio     = $newWidth / $width;
        $newHeight = ceil($height * $ratio);

        $temporary = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($temporary, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Buffer
        ob_start();

        switch ($file->getExtension()) {
            case "png":
                imagepng($temporary);
                break;
            case "jpg":
            case "jpeg":
                imagejpeg($temporary);
                break;
        }

        $image = ob_get_contents();
        ob_end_clean();

        imagedestroy($temporary);

        return $image ?? null;
    }
}