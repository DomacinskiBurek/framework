<?php

namespace DomacinskiBurek\System;

class Session
{
    protected int $expire = 1440;
    protected string $string;

    function __construct ()
    {
        ini_set('gc_divisor', 100);
        ini_set('gc_maxlifetime', $this->expire);
        ini_set('gc_probability', 1);
        ini_set('sid_length', 32);

        $this->initialize();
    }

    public function set (string $sessionName, string $sessionValue): void
    {
        $_SESSION[$sessionName] = $sessionValue;
    }

    public function get (string $sessionName) : ?string
    {
        return $_SESSION[$sessionName] ?? null;
    }

    public function has (string $sessionName) : bool
    {
        return isset($_SESSION[$sessionName]);
    }

    public function clear () : void
    {
        session_unset();
    }

    public function delete (string $sessionName): void
    {
        if(isset($_SESSION[$sessionName])) unset($_SESSION[$sessionName]);
    }

    public function hashKey (string $prefix = 'S') : string
    {
        return sprintf('%s-%04x%04x-%04x%04x%04x-%04x%04x',
            $prefix, mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff) | 0x4000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function status () : bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private function initialize () : void
    {
        if (!$this->status()) session_start();
    }
}