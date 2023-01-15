<?php

namespace DomacinskiBurek\Application\Default\Model;

use DomacinskiBurek\System\Model;

class Health extends Model
{
    public string $loginName;
    public string $loginPassword;
    protected function marshalProperties (): array
    {
        return [
            "login_name"     => "loginName",
            "login_password" => "loginPassword"
        ];
    }
}