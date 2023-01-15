<?php

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\Error\Handlers\ModelPropertyUndefined;
use ReflectionClass;
use ReflectionException;
use TypeError;

abstract class Model
{
    private ReflectionClass $reflect;
    public function __construct ()
    {
        $this->reflect = new ReflectionClass($this);
    }

    /**
     * @throws ModelPropertyUndefined
     * @throws ReflectionException
     */
    public function __set(string $property, $propertyValue)
    {
        if ($this->containProperty($property) === false) throw new ModelPropertyUndefined();

        $reflectProperty = $this->reflect->getProperty($property);
        if (gettype($propertyValue) != $reflectProperty->getType()) throw new TypeError("could not set type");

        $this->{$property} = $propertyValue;
    }

    /**
     * @throws ModelPropertyUndefined
     */
    public function __get(string $property = "")
    {
        if ($this->containProperty($property) === false) throw new ModelPropertyUndefined();

        return $this->{$property};
    }

    /**
     * @throws ModelPropertyUndefined
     * @throws ReflectionException
     */
    public function __bind (array $fields): void
    {
        $fieldMap = $this->marshalMap($fields);

        foreach ($fieldMap as $property => $propertyValue) {
            $this->__set($property, $propertyValue);
        }
    }

    protected function marshalMap(array $fields): array
    {
        $properties = $this->marshalProperties();

        foreach ($properties as $key => $item) {
            unset($properties[$key]);
            $properties[$item] = empty($fields[$key]) ? "" : $fields[$key];
        }

        return $properties;
    }
    protected function containProperty (string $property): bool
    {
        return property_exists($this, $property);
    }

    abstract protected function marshalProperties (): array;
}