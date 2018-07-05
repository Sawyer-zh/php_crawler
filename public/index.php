<?php

require __DIR__ . '/../vendor/autoload.php';
$url = 'http://www.mutouw.com';

$obj = new Jrw\Crawler\TestCrawler($url);

$obj->addBaseUrl();


$reflectionClass = new ReflectionClass($obj);

$crawler = $reflectionClass->newInstance($url);

$crawler->run();
exit;

$crawler = new Jrw\Crawler\TestCrawler;

$test = new Jrw\Test\Test;
// $ret = $crawler->doRequest("https://tiku.baidu.com/web/quedetail/ebfc4c11f18583d0496459c9");

$ret = $test->testDoRequest();

var_dump($ret);

$ret = $test->testSave2db($test->url, $ret);

var_dump($ret);

$ret = $test->testgetlatest();

var_dump($ret);

// $ret = iconv('utf-8', 'gbk/IGNORE',  $ret);
// var_dump($ret);

// $cursor = $crawler->getLastest();
// var_dump($cursor);

// $redis = new Jrw\Core\Redis();

// var_dump($redis->ping());

// $crawler = new Jrw\Crawler\TestCrawler;

// $crawler->run();
// $crawler->find();
