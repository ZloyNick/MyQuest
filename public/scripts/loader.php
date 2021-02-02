<?php

function loadAll(string $dir) : void
{
    $files = scandir($dir);
    unset($files[0], $files[1]);

    foreach ($files as $file)
    {
        if(!is_dir($dir.$file) && stripos($file, '.php'))
            require_once $dir.$file;
        else loadAll($dir.$file.DIRECTORY_SEPARATOR);
    }

}

loadAll(__DIR__ . '/../../app/Lib/ZloyNick/pthreads/');
