<?php

namespace Jrw\Core;

class Redis
{

    private $_redis;

    public function __construct()
    {
        $config = require __DIR__ . '/../redis.php';
        $this->_redis = new \Redis();
        $this->_redis->connect($config['host'], $config['port']);
    }

}
