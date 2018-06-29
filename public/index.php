<?php

var_dump(hex2bin($str));

exit;
require __DIR__ . '/../bootstrap/autoload.php';


use MongoDB\Driver\Manager;
$m = new Manager("mongodb://root:root@mongodb/admin");
var_dump($m);

$bulk = new MongoDB\Driver\BulkWrite;
$document = ['_id' => new MongoDB\BSON\ObjectID, 'name' => 'whtat'];

$_id= $bulk->insert($document);
$m->executeBulkWrite('zm_crawler.zm',$bulk);

var_dump($_id);
