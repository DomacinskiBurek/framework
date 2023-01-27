<?php

namespace DomacinskiBurek\Application\Auth\Login\Model;

use DomacinskiBurek\System\Model;

class Login extends Model
{
    protected string $username;
    protected string $password;
    protected function marshalProperties(): array
    {
        return [
            "loginUsername" => "username",
            "loginPassword" => "password"
        ];
    }
}