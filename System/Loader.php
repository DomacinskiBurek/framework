<?php

namespace DomacinskiBurek\System;

require_once __DIR__ . '/Helpers.php';

use DomacinskiBurek\Application\Health\Health;
use DomacinskiBurek\System\Config\Config;
use DomacinskiBurek\System\Error\Handlers\InstanceNotCallable;
use ReflectionClass;
use ReflectionException;

class Loader
{
    public function __construct
    (
        public Route $route             = new Route(),
        public Request $request         = new Request()
    )
    {
        Config::includeConfig("config");
    }

    public function run (): string
    {
        $this->registerRoutes($this->route);

        if (!$this->request->isOrigin()) $this->request->redirect($this->request->origin());
        $routeCallback = $this->route->get($this->request->method(), $this->request->getOriginPath(), $this->request);

        if (!is_callable($routeCallback)) return view(["error" => "not found"], [], 404);

        return $this->runCallback($routeCallback);
    }

    private function registerRoutes (Route $route) : void
    {
        $route->register("get", "/", "index",
            fn(string $method) => fn() => $this->setCallBack(Health::class, false, $method)
        );

        $app = $route->groupBy("/health");
        $app->register("get", "/run", "index",
            fn(string $method) => fn() => $this->setCallBack(Health::class, false, $method)
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