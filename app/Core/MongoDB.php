<?php

namespace Jrw\Core;

use MongoDB\Client;

class MongoDB
{

    protected $_config;

    public $client;

    public function __construct()
    {
        $this->_config = require __DIR__ . '/../Conf/mongodb.php';
        $this->client = new Client($this->_config['connection']);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->client, $name), $args);
    }

    public function __get($name)
    {
        return $this->client->$name;
    }

}
