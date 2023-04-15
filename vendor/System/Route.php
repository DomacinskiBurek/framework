<?php

declare(strict_types=1);

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\Error\Handlers\RouteMethodNotExist;
use DomacinskiBurek\System\Error\Handlers\RouteNotExists;

class Route
{
    private array $routeList = [
        "get"    => [],
        "post"   => [],
        "delete" => [],
        "put"    => []
    ];

    /**
     * @throws RouteMethodNotExist
     */
    public function set (string $method, string $route, string $cmethod, callable $callback, array $group = []): void
    {
        if ($this->hasMethod($method) === false) throw new RouteMethodNotExist ();

        foreach ($group as $gRoute => $gMethod) {
            $this->set($method, trim($route, "/") . "/" . ltrim($gRoute, "/"), $gMethod, $callback);
        }

        $this->routeList[$method][$route] = $callback($cmethod);
    }

    /**
     * @throws RouteNotExists
     * @throws RouteMethodNotExist
     */
    public function get (string $method, string $route, ?Request $request = null)
    {
        $callback = $this->has($method, $route, $request);

        if ($callback === false) throw new RouteNotExists();

        return $callback;
    }

    /**
     * @throws RouteMethodNotExist
     */
    public function has (string $method, string $route, ?Request $request = null)
    {
        if ($this->hasMethod($method) === false) throw new RouteMethodNotExist ();

        $routeList = $this->routeList[$method];

        foreach ($routeList as $assignedRoute => $returnCallback) {
            if ($this->isRegexRoute($assignedRoute)) { //&& $this->isRegexRoute($route)
                if ($this->matchRegexRoute($assignedRoute, $route, $request)) return $returnCallback;
            } else if ($assignedRoute === $route) {
                return $returnCallback;
            }
        }

        return null;
    }

    protected function hasMethod (string $method): bool
    {
        return array_key_exists($method, $this->routeList);
    }

    protected function matchRegexRoute (string $assignedRoute, $route, ?Request $request = null): bool
    {
        $routeParams = null;

        if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $assignedRoute, $found)) $routeParams = $found[1];

        if (preg_match_all($this->buildRegexString($assignedRoute), $route, $found)) {
            $regexParams = [];

            $iterate = 0;
            while (++$iterate < count($found)) $regexParams[] = $found[$iterate][0];

            if (is_null($request) === false) $request->setParams($request->method(), array_combine($routeParams, $regexParams));

            return true;
        }

        return false;
    }

    protected function buildRegexString (string $assignedRoute): string
    {
        return "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "($m[2])" : '(\w+)', $assignedRoute) . "$@";
    }

    protected function isRegexRoute (string $route) : bool
    {
        return (bool) preg_match('/\{(\w+)(:[^}]+)?}/', $route);
    }
}