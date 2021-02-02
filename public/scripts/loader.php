<?php

declare(strict_types=1);

function loadAll(string $dir) : bool
{
    // scanning library's directory
    $files = scandir($dir);
    // bug fixing, damn developers of php...
    unset($files[0], $files[1]);

    foreach ($files as $file) {
        if(!is_dir($dir.$file) && stripos($file, '.php'))
            require_once $dir.$file;
        else loadAll($dir.$file.DIRECTORY_SEPARATOR);
    }

    return true;
}

return loadAll(__DIR__ . '/../../app/Lib/ZloyNick/pthreads/');
