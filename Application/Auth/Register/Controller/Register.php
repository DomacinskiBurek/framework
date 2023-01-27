<?php

namespace DomacinskiBurek\Application\Auth\Register\Controller;

use DomacinskiBurek\System\Controller;
use DomacinskiBurek\System\Request;
use DomacinskiBurek\System\Route;
use DomacinskiBurek\System\View;
use Exception;

class Register extends Controller
{
    public function __construct (private Request $request, private Route $route){}

    /**
     * @throws Exception
     */
    public function index()
    {
        return View::render("Auth.Register.Page");
    }
}