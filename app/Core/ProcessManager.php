<?php

namespace Jrw\Core;

use Swoole\Process\Pool;
use ReflectionClass;

class ProcessManager
{

    private $_pool;

    private $_conf;

    private $_crawler;

    private $_masterPid;

    private $_class;

    public function __construct($class)
    {
        $this->_conf = require __Dir__ . '/../Conf/process.php';
        $this->_pool = new Pool($this->_conf['num'], $this->_conf['ipc_type']);
        if (!is_subclass_of($class, 'Jrw\Crawler\Crawler')) {
            exit("the given class should be the subclass of Jrw\\Crawler\\Crawler");
        }
        $this->_class = $class;
        $this->_masterPid = posix_getpid();
    }

    public function start()
    {
        $this->_pool->on('WorkerStart', function ($pool, $workerId) {
            $reflectionClass = new ReflectionClass($this->_class);
            $this->_crawler = $reflectionClass->newInstance();

            if ($this->isFinish()) {
                $this->stop();
            }

            $this->_crawler->run();

        });
        $this->_pool->on('WorkerStop', function ($pool, $workerId) {

        });
        $this->_pool->start();
    }

    public function stop()
    {
        posix_kill($this->_masterPid, SIGTERM);
    }

    public function restart()
    {
        posix_kill($this->_masterPid, SIGUS1);
    }

    public function isFinish()
    {
        $ret = $this->_crawler->_redis->zCard($this->_crawler->_key) === 0;

        if ($ret) {
            sleep(30);
            $retWait = $this->_crawler->_redis->zCard($this->_crawler->_key) === 0;
        }
        return $ret && $retWait;
    }
}
