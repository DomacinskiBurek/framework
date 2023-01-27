<?php

namespace DomacinskiBurek\Application\Auth\Login\Controller;

use DomacinskiBurek\System\Controller;
use DomacinskiBurek\System\Database;
use DomacinskiBurek\System\Error\Handlers\DatabaseException;
use DomacinskiBurek\System\Error\Handlers\ModelPropertyUndefined;
use DomacinskiBurek\System\Query;
use DomacinskiBurek\System\Request;
use DomacinskiBurek\System\Route;
use DomacinskiBurek\System\View;
use DomacinskiBurek\Application\Auth\Login\Model\Login as LoginModel;
use Exception;
use PDO;
use ReflectionException;

class Login extends Controller
{
    public function __construct (private Request $request, private Route $route){}

    /**
     * @throws Exception
     */
    public function index()
    {
        return View::render("Auth.Login.Page");
    }

    /**
     * @throws ReflectionException
     * @throws ModelPropertyUndefined
     * @throws DatabaseException
     * @throws Exception
     */
    public function login ()
    {
        $database = Database::connect();

        $loginModel = new LoginModel();
        $loginModel->__bind($this->request->getParams($this->request->method()));

        $prepare_sql = $database->prepare(Query::generate("Select::user_login_check", $loginModel));
        $prepare_sql->execute();

        $fetch_sql   = $prepare_sql->fetch(PDO::FETCH_OBJ);
        if (empty($fetch_sql)) return View::render(["message" => "could not find user"], null, 400);

        
    }
}