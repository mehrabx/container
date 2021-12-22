<?php


namespace Mehrabx\Container\Core;

use ReflectionClass;

class AutomateInjection
{

    /**
     * @throws \ReflectionException
     */
    public function resolve(string $class)
    {
        $reflection = new ReflectionClass($class);

        if (($constructor = $reflection->getConstructor()) == null) {
            return $reflection->newInstance();
        }

        if (($params = $constructor->getParameters()) == []) {
            return $reflection->newInstance();
        }

        $newInstanceParams = [];
        foreach ($params as $param) {
            $newInstanceParams[] = $param->getClass() == null
                ? $param->getDefaultValue()
                : $this->resolve($param->getClass()->getName());
        }

        return $reflection->newInstanceArgs($newInstanceParams);

    }

}