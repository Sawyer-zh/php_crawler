<?php

namespace Jrw;

class Application
{

    private static $_app;

    protected $buildStack = [];

    protected $with = [];

    private function __construct()
    {

    }

    public static function instance()
    {
        if (is_null(self::$_app)) {
            self::$_app = new self;
        }
        return self::$_app;
    }

    public function make($class)
    {

        if ($class == self::class) {
            return self::$_app;
        }

        $this->buildStack[$class] = $class;
        $reflectionClass = new ReflectionClass($class);

        $constructor = $reflectionClass->getConstrutor();

        if (is_null($constructor)) {
            array_pop($buildStack[$class]);
            return $reflectionClass->newInstance();
        }

        $params = $constructor->getParameters();
        if (!$params) {
            array_pop($buildStack[$class]);
            return $reflectionClass->newInstance();
        }

        foreach ($params as $reflectionPara) {
            $this->with[$class][] = self::make($reflectionPara->getClass()->name);
        }

        $args = array_pop($this->with[$class]);
        return $reflectionClass->newInstanceArgs($args);
    }
}
