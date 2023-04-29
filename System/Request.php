<?php

declare(strict_types=1);

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\Config\Config;
use JetBrains\PhpStorm\NoReturn;

class Request
{
    private array $params = [];
    private array $upload = [];

    public function __construct()
    {
        $this->parseRequestParams();
    }

    public function method () : string
    {
        return strtolower($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function code (int $code = 200) : void
    {
        http_response_code($code);
    }

    public function header (string $content_type = 'text/html;charset=utf-8') : void
    {
        if (is_null($this->has($content_type))) header("Content-Type: $content_type");
    }

    #[NoReturn] public function redirect (string $uri) : void
    {
        header("Location: $uri");
        exit;
    }

    public function origin () : string // Route like folder
    {
        return Config::get("SERVER_HOST", "config");
    }

    public function getOriginPath()
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function isOrigin (): bool
    {
        $_SERVER['REQUEST_SCHEME'] ??= 'http';

        return $this->origin() === "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";
    }

    public function setParams (string $method, array $field) : void
    {
        if (array_key_exists($method, $this->params) === false) $this->params[$method] = [];

        $this->params[$method] = array_merge($this->params[$method], $field);
    }

    public function getParam (string $method, $param)
    {
        return array_key_exists($method, $this->params) ? $this->params[$method][$param] ?? null : null;
    }

    public function getParams (string $method)
    {
        return array_key_exists($method, $this->params) ? $this->params[$method] ?? null : null;
    }

    public function getFile (?string $param = null)
    {
        return array_key_exists($param ?? array_key_first($this->upload), $this->upload) ? $this->upload[$param ?? array_key_first($this->upload)] : null;
    }

    public function has (string $header)
    {
        $forge = [];
        foreach(getallheaders() as $key => $value) {
            $forge[$key] = "$key:$value";
        }

        $forge = array_filter(array_map(fn($string) => strtolower($string), $forge), fn($string) => str_contains($string, $header));

        return (empty($forge)) ? null : getallheaders()[array_key_first($forge)];
    }

    // Depreceated
    public function getAuthBearer (): ?string
    {
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'] ?? "", $matches)) return null;

        return $matches[1];
    }

    //public function has

    private function parseRequestParams (): void
    {
        if (!is_null($this->has("multipart/form-data"))) $this->parseFileUpload();
        switch ($this->method()) {
            case 'get':
            case 'delete':
                $this->setParams($this->method(), $_GET);
                break;
            default:
                $this->setParams($this->method(), $_GET);
                $this->setParams($this->method(), $_POST);

                $memoryList = json_decode(file_get_contents("php://input"), true);
                if (!is_null($memoryList)) $this->setParams($this->method(), $memoryList);
                break;
        }
    }

    private function parseFileUpload(): void
    {
        foreach ($_FILES as $key => $file) {
            $filename  = substr($file["name"], 0, strpos($file["name"], '.'));
            $extension = substr($file["name"], strlen($filename) + 1);

            $this->upload[$key] = [
                "name"          => $filename,
                "extension"     => $extension,
                "size"          => $file['size'],
                "type"          => $file['type'],
                "tmp_location"  => $file['tmp_name'],
                "error"         => $file['error']
            ];
        }
    }
}