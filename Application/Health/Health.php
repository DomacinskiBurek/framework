<?php

namespace DomacinskiBurek\Application\Health;

use DomacinskiBurek\System\Controller;
use Exception;

class Health extends Controller
{

    /**
     * @throws Exception
     */
    public function index()
    {
        return view("Auth.login");
    }
}