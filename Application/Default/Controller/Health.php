<?php
namespace DomacinskiBurek\Application\Default\Controller;

use DomacinskiBurek\System\Controller;
use DomacinskiBurek\System\Error\Handlers\DatabaseNotFound;
use DomacinskiBurek\System\View;

class Health extends Controller
{

    /**
     * @throws DatabaseNotFound
     */
    public function index()
    {
        return View::render(["status" => "success", "message" => date("d M Y H:i")]);
    }
}