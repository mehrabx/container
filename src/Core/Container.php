<?php


namespace Mehrabx\Container\Core;

use Exception;
use Mehrabx\Container\Core\AutomateInjection;

class Container
{

    public static $binds = [];

    protected static $singletons = [];

    protected $autoInjection;


    public function bind($key, $value)
    {
        $this->setService($key, $value, 'bind');
    }

    public function singleton($key, $value)
    {
        $this->setService($key, $value, 'singleton');
    }

    public function instance($key, $value)
    {
        $this->setService($key, $value, 'instance');
    }

    public function make($value)
    {
        if (self::$binds[$value]) {

            switch (self::$binds[$value]['type']) {
                case 'bind' :
                    return $this->callBinding($value);
                    break;
                case 'singleton' :
                    return $this->callSingleton($value);;
                    break;
                case 'instance' :
                    return $this->callInstance($value);
                    break;
            }
        } else {
            throw new \Exception("$value not defined in container");
        }
    }


    public function callBinding($value)
    {
        return call_user_func(self::$binds[$value]['value'], $this);
    }


    public function callSingleton($value)
    {
        if (!array_key_exists($value, self::$singletons)) {
            self::$singletons[$value] = call_user_func(self::$binds[$value]['value'], $this);
        }
        return self::$singletons[$value];
    }


    public function callInstance($value)
    {
        return self::$binds[$value]['value'];
    }


    public function setService($key, $value, $type): void
    {
        $this->isBindKeySet($key);

        self::$binds[$key]['value'] = $value;
        self::$binds[$key]['type'] = $type;
    }


    public function isBindKeySet($key): void
    {
        if (isset(self::$binds[$key])) {
            throw new \Exception("$key has been defined before");
        }
    }

    public function exist(string $value): bool
    {
        return array_key_exists($value, self::$binds);
    }

}
