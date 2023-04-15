<?php

namespace DomacinskiBurek\System;

require_once __DIR__ . '/Helpers.php';

use DomacinskiBurek\Application\API\Controller\Invoke;
use DomacinskiBurek\Application\Auth\Login\Controller\Login;
use DomacinskiBurek\Application\Auth\Register\Controller\Register;
use DomacinskiBurek\Application\Default\Controller\Health;
use DomacinskiBurek\System\Error\Handlers\InstanceNotCallable;
use DomacinskiBurek\System\Error\Handlers\RouteMethodNotExist;
use ParseError;
use ReflectionClass;
use ReflectionException;

class Loader
{
    public function __construct
    (
        public Route $route             = new Route(),
        public Request $request         = new Request(),
        private readonly Config $config = new Config()
    )
    {
        $this->config->load("config", "yaml");
    }

    /**
     * @throws RouteMethodNotExist|Error\Handlers\RouteNotExists
     */
    public function run (): void
    {
        $this->registerRoutes($this->route);

        if ($this->request->urlHost($this->config->get("config", "SERVER_HOST")) === false) $this->request->redirect($this->config->get("config", "SERVER_HOST"));
        if (str_contains(strtolower($this->request->url('/')), 'public')) throw new ParseError('Project cannot be accessed directly. Please contact author of the project!');

        $route = $this->route->get($this->request->method(), $this->request->url(''), $this->request);
        if (is_null($route)) $route = function () { $this->request->responseCode(404); return ["message" => "unauthorized access"];};

        $instance = $this->runCallback($route);

        if (is_object($instance) || is_array($instance)) {
            exit(
                json_encode(
                    $instance
                )
            );
        } else if (is_string($instance)) {
            exit(
                $instance
            );
        }
    }

    /**
     * @throws RouteMethodNotExist
     */
    private function registerRoutes (Route $route) : void
    {
        // Get
        $route->set("get", "/", "index",
            fn(string $method) => fn() => $this->setCallBack(Invoke::class, false, $method)
        );
    }

    private function runCallback ($callback) {
        if (!is_callable($callback)) return ["not callable"];

        return call_user_func($callback);
    }
    /**
     * @throws InstanceNotCallable
     * @throws ReflectionException
     */
    private function setCallBack (string $class, bool $constructor, string $method, ...$args)
    {
        $callable = new ReflectionClass($class);
        $callable = $constructor ? $callable->newInstanceArgs($args) : $callable->newInstanceWithoutConstructor();

        return call_user_func([$callable, $method]);
    }
}