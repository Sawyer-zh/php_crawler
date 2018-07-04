<?php

namespace Jrw\Core;

use Guzzle\Http\Client;

class Request
{

    private $_client;

    public function __construct()
    {
        $this->_client = new Client();

    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->_client, $name), $args);
    }

}
