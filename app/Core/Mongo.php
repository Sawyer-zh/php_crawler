<?php

namespace Jrw\Model;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;

class Mongo
{

    protected $_config;

    protected $_db;

    protected $_collection;

    public static $instance;

    public $manager;

    public $bulk;

    public $query;

    protected $_where = [];

    public function __construct()
    {
        $this->_config = require __DIR__ . '/../Conf/mongo.php';
        $this->manager = new Manager($this->_config['connection']);
        $this->bulk = new BulkWrite();
    }

}
