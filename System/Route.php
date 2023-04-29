<?php

declare(strict_types=1);

namespace DomacinskiBurek\System;

class Route
{
    public array $routeList = [];
    private string $groupBy = "";

    public function __set(string $name, mixed $value) {}
    public function __get(string $name) {}

    public function groupBy (string $route) : self
    {
        $this->groupBy = (!empty($this->groupBy)) ? implode("", [$this->groupBy, $route]) : $route;

        return $this;
    }

    public function register (string $method, string $route, string $classMethod, callable $callback): void
    {
        if (!array_key_exists($method, $this->routeList)) $this->routeList[$method] = [];

        $this->routeList[$method][implode("", [$this->groupBy, $route])] = $callback($classMethod);
    }

    public function get (string $method, string $route, ?Request $request = null)
    {
        $routeList = $this->routeList[$method] ?? [];

        foreach ($routeList as $assignedRoute => $callback) {
            if ($this->isRegexRoute($assignedRoute)) { //&& $this->isRegexRoute($route)
                if ($this->matchRegexRoute($assignedRoute, $route, $request)) return $callback;
            } else if ($assignedRoute === $route) {
                return $callback;
            }
        }

        return null;
    }


    protected function matchRegexRoute (string $assignedRoute, $route, ?Request $request = null): bool
    {
        $routeParams = null;

        if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $assignedRoute, $found)) $routeParams = $found[1];

        if (preg_match_all($this->buildRegexString($assignedRoute), $route, $found)) {
            $regexParams = [];

            $iterate = 0;
            while (++$iterate < count($found)) $regexParams[] = $found[$iterate][0];

            if (!is_null($request)) $request->setParams($request->method(), array_combine($routeParams, $regexParams));

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