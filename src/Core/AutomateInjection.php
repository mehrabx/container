<?php


namespace Mehrabx\Container\Core;

use ReflectionClass;

class AutomateInjection
{

    public $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function resolve(string $class, array $args = [])
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

            if (array_key_exists($param->name, $args)) {
                $newInstanceParams[] = $args[$param->name];
            } else {
                if ($param->getType() == null) {
                    $newInstanceParams[] = $param->getDefaultValue();
                } else {

                    $newInstanceParams[] = $this->container->exist(@$param->getClass()->name)
                        ? $this->container->make(@$param->getClass()->name)
                        : $this->resolve(@$param->getClass()->name);

                }
            }

        }

        return $reflection->newInstanceArgs($newInstanceParams);

    }

}