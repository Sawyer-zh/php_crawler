<?php

require __DIR__ . '/vendor/autoload.php';

$pool = new Jrw\Core\ProcessManager('Jrw\Crawler\TestCrawler');

$pool->start();
