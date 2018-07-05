<?php

require __DIR__ . '/vendor/autoload.php';

$options = getopt('u:');

$url = $options['u'];

$obj = new Jrw\Crawler\TestCrawler($url);

if (!$obj->addBaseUrl()) {
    exit('can not add base url');
}

$pool = new Jrw\Core\ProcessManager($obj, $url);

$pool->start();
