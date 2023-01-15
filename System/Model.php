<?php

namespace DomacinskiBurek\System;

abstract class Model
{
    abstract public function __set (string $property, $propertyValue);
    abstract public function __get (string $property);
    abstract protected function marshalMap (array $fields);
}