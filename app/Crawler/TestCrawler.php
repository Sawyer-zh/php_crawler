<?php

namespace Jrw\Crawler;

class TestCrawler extends Crawler
{

    // protected $_baseUrl = "http://www.baidu.com";

    public function __construct()
    {
        $this->_table = md5($this->_baseUrl);
        parent::__construct();
    }

    public function save2db($url, $res)
    {

        $ret = $this->_mongodb->{$this->_mongodbName}->{$this->_table}->insertOne(
            [
                "url" => $url,
                "time" => time(),
                "content" => $res,
                "md5" => md5($res),
            ]
        );
        return $ret->getInsertedCount();
    }

    public function getLatest()
    {
        $obj = $this->_collection->findOne(
            [],
            [
                'limit' => 1,
                'sort' => ['time' => -1],
            ]
        );
        return $obj;
    }
}
