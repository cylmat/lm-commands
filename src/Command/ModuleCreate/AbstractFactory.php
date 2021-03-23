<?php

namespace LmConsole\Command\ModuleCreate;

class AbstractFactory
{
    public static function __callStatic(string $className, $params=[])
    {
        $className = __NAMESPACE__ . "\\" . $className;
        if (class_exists($className)) {
            return new $className(...$params);
        }
        throw new \DomainException("Class $className doesn't exists in factory.");
    }
}