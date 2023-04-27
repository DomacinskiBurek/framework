<?php

namespace DomacinskiBurek\Application\Auth\Controller;

use DomacinskiBurek\System\Controller;
use DomacinskiBurek\System\Request;
use Exception;

class Auth extends Controller
{
    public function __construct
    (
        private Request $request
    ){}

    public function index() {}

    /**
     * @throws Exception
     */
    public function login ()
    {
        return view("Auth.Login");
    }
}