<?php

use DomacinskiBurek\System\Config;
use DomacinskiBurek\System\Database;
use DomacinskiBurek\System\Error\Handlers\DatabaseException;
use DomacinskiBurek\System\Language;
use DomacinskiBurek\System\System;
use DomacinskiBurek\System\User;

if (!function_exists('language')) {

    function language (string $line, array $args = [])
    {
        $language = new Language();
        $language->setLocale(System::getActiveLanguage());

        return $language->translate($line, $args);
    }
}

if (!function_exists('sidebarmenu')) {
    /**
     * @throws DatabaseException
     * @throws Exception
     */
    function sidebarmenu ()
    {
        $user = new User();

        $is_logged = $user->isLogged();
        if (!$is_logged) {
            return null;
        }

        return $user->UserAccess($is_logged);
    }
}

if (!function_exists('userdetails')) {

    /**
     * @throws Exception
     */
    function userdetails (?int $user_id = null)
    {
        $user = new User();

        $is_logged = $user->isLogged();
        if ($is_logged === false) {
            return null;
        }

        return $user->UserDetails((is_null($user_id)) ? $is_logged : $user_id);
    }
}

if (!function_exists('languagelist')) {
    /**
     * @throws Exception
     */
    function languagelist ()
    {
        return System::getLanguageList();
    }
}

if (!function_exists('languagelocale')) {
    function languagelocale ()
    {
        return Language::getLanguageLocale();
    }
}

if (!function_exists('generalconfig')) {
    function generalconfig ()
    {
        $config = new Config();
        $config->load("config", "yaml");

        return $config->get('config');
    }
}