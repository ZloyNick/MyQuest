<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'loader.php';

list($phpBin, $threads, $inn, $dadataTokenKey, $blockedServicesString) = $argv;

$blockedServices = explode(',', substr($blockedServicesString, 0));

//$threads = 12;
//$inn = '2310031475';
//$dadataTokenKey = '77f853c4199da17ae98aeead1dbdb4cc95b98494';
//$blockedServices = [];

echo serialize(
    (new App\Lib\ZloyNick\pthreads\Scheduler)
        ->init(
            [
                'threads' => (int)$threads,
                'inn' => $inn,
                'dadataTokenKey' => $dadataTokenKey,
                'services.blocked' => $blockedServices
            ]
        )->run()->onComplete()
);
