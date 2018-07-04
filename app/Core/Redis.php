<?php

namespace Jrw\Core;

class Redis
{

    private $_redis;

    public function __construct()
    {
        $config = require __DIR__ . '/../Conf/redis.php';
        $this->_redis = new \Redis();
        $this->_redis->pconnect($config['host'], $config['port']);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->_redis, $name), $args);
    }

}
