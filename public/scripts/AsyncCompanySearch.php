<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'loader.php';

list($phpBin, $threads, $inn, $dadataTokenKey, $blockedServicesString) = $argv;

$blockedServices = explode(',', substr($blockedServicesString, 0));

$scheduler = new App\Lib\ZloyNick\pthreads\Scheduler;

$scheduler->init([
    'threads' => (int)$threads,
    'inn' => $inn,
    'dadataTokenKey' => $dadataTokenKey,
    'services.blocked' => $blockedServices
]);

$data = [];

$scheduler->run();
$scheduler->onComplete($data, true);

echo serialize($data);
