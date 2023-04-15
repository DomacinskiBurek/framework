<?php

namespace DomacinskiBurek\Application\API\Controller;

use DomacinskiBurek\System\Controller;
use DomacinskiBurek\System\Request;
use DomacinskiBurek\System\RESTful\APIToken;
use DomacinskiBurek\System\View;
use Exception;

class Invoke extends Controller
{
    public function __construct (private Request $request, private Route $route)
    {

    }

    /**
     * @throws Exception
     */
    public function index()
    {
        $api = new APIToken();
        print_r($api->createToken());
        return View::render(["_ok" => true, "message" => date("d M Y")]);
    }
}