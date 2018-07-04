<?php

namespace Jrw\Crawler;

use Jrw\Core\MongoDB;
use Jrw\Core\Redis;
use Jrw\Core\Request;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

abstract class Crawler implements CrawlerState
{
    protected $_baseUrl;

    protected $_redis;

    protected $_mongodb;

    protected $_requestClient;

    protected $_mongodbName = "zm";

    protected $_domCrawler;

    protected $_key;

    protected $_table;

    protected $_collection;

    const RETRY_TIMES = 3;

    const QUEUE_TODO_PRIFIX = 'queue::';
    const QUEUE_FINISH_PRIFIX = 'queue::finish::';
    const QUEUE_FAIL_PRIFIX = 'queue::fail::';

    public function __construct()
    {
        $this->_redis = new Redis();
        $this->_mongodb = new MongoDB();
        $this->_collection = $this->_mongodb->{$this->_mongodbName}->{$this->_table};
        $this->_requestClient = new Request();
        $this->addBaseUrl();
    }

    public function addBaseUrl()
    {
        if (empty($this->_baseUrl)) {
            exit("please add the root website address");
        }

        $this->_key = self::QUEUE_TODO_PRIFIX . $this->_table;

        $element = array(
            'url' => $this->_baseUrl,
            'attempts' => 0,
        );

        $this->_redis->zAdd($this->_key, time(), json_encode($element));

    }

    public function getUrl()
    {
        $ret = $this->_redis->zRange($this->_key, 0, 0);
        if (empty($ret)) {
            exit("empty todo queue");
        }
        $this->_redis->zRemRangeByRank($this->_key, 0, 0);
        $element = json_decode($ret[0], true);
        return $element;
    }

    public function doRequest($element)
    {
        $url = $element['url'];
        $request = $this->_requestClient->get($url);
        $response = $request->send();
        if (($code = $response->getStatusCode()) != '200') {
            $element['code'] = $code;
            $this->handlerErrorRequest($element);
        }
        return $response->getBody(true);
    }

    public function handlerErrorRequest($element)
    {
        if ($element['attemps'] >= self::RETRY_TIMES) {
            $this->_redis->sAdd(self::QUEUE_FAIL_PRIFIX . $this->_table, json_encode($element));
        } else {
            $element['attemps']++;
            $this->_redis->zAdd($this->_key, time(), json_encode($element));
        }
        exit;
    }

    public function run()
    {
        $element = $this->getUrl();
        $ret = $this->doRequest($element);

        if ($this->_redis->sIsMember(self::QUEUE_FINISH_PRIFIX . $this->_table, $element['url'])) {
            exit("Aready crawlered!");
        }

        $this->addNewElements($ret);

        $this->save2db($element['url'], $ret);
        $this->handleFinish($element['url']);
    }

    public function addNewElements($ret)
    {
        $this->_domCrawler = new DomCrawler($ret);

        $alinks = $this->_domCrawler->filterXpath('//a')->extract('href');
        foreach ($alinks as $a) {
            $url = $this->getInnerLink($a);
            if ($url && !$this->_redis->sIsMember(self::QUEUE_FINISH_PRIFIX . $this->_table, $url)) {
                echo $url;
                $element = array(
                    'url' => $url,
                    'attemps' => 0,
                );
                $this->_redis->zAdd($this->_key, time(), json_encode($element));
            }
        }
    }

    public function handleFinish($url)
    {
        $this->_redis->sAdd(self::QUEUE_FINISH_PRIFIX . md5($this->_baseUrl), $url);
    }

    public function getInnerLink($url)
    {
        $name = explode('.', $this->_baseUrl);
        $domain = $name[1];

        if (strpos($url, $domain) === false) {
            return false;
        }

        if (strpos($url, "http") !== false) {
            return $url;
        }

        return $this->_baseUrl . '/' . trim($url, '/');
    }

    public function __get($name)
    {
        return $this->$name;
    }

    abstract public function save2db($url, $ret);

    public function getQueued()
    {
        return $this->_redis->zCard(self::QUEUE_TODO_PRIFIX . $this->_table);

    }

    public function getFinished()
    {
        return $this->_redis->sCard(self::QUEUE_FINISH_PRIFIX . $this->_table);
    }

    public function getFailed()
    {
        return $this->_redis->sCard(self::QUEUE_FAIL_PRIFIX . $this->_table);
    }

    public function getSaved()
    {
        return $this->_collection->find([]);
    }
}
