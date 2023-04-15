<?php

namespace DomacinskiBurek\Application\Health;

use DomacinskiBurek\System\Controller;
use DomacinskiBurek\System\View;
use Exception;

class Health extends Controller
{

    /**
     * @throws Exception
     */
    public function index()
    {
        return View::render(["_ok" => true]);
    }
}