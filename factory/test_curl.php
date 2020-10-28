<?php
require_once '../vendor/autoload.php';

use Models\MasterReport;

$obj = new MasterReport();

$start = microtime(TRUE);

$call = $obj->parseProperty('300.01', 'float');

$end = microtime(TRUE);

echo ($end-$start);
var_dump($call);
