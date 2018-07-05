#!/usr/local/bin/php
<?php

$allelements = require __dir__ . '/app/Conf/urls.php';

$urls = array_column($allelements, 'url');

foreach ($urls as $url) {
    $filename = md5($url) . '.txt';
    exec("php cli.php -u {$url} >> $filename  2>&1");
    sleep(1);
}
