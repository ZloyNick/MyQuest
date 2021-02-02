<?php

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

/**
 *
 * <title>
 *  Quick replacement without overriding when used.
 * </title>
 *
 * @param string $replace
 * @param string $current
 * @param string $text
 *
 */
function rplc(string $replace, string $current, string &$text) : void
{
    $text = str_replace($replace, $current, $text);
}

use App\Lib\ZloyNick\pthreads\task\DadataAsyncTask;
use App\Lib\ZloyNick\pthreads\task\LocalRestAsyncTask;

use App\Lib\ZloyNick\pthreads\task\RandomDataAsyncTask;
use function str_replace, in_array;

class Scheduler
{

    /** @var AsyncPool */
    private $asyncPool;
    /**
     * <p>
     *  Dadata Token, INN
     * </p>
     * @var string[]
     */
    private $data = [];
    private static $blocked = [];

    function init(array $data = [])
    {
        $this->logFile = __DIR__.'/../../../../storage/logs/ZloyNick_pthreads.log';

        if(!isset($data['inn']) || !isset($data['dadataTokenKey']) || !isset($data['threads']))
        {
            $this->generateErrorMessage(
                __FILE__,
                'Cannot found one of index: inn, dadataTokenKey or threads',
                __METHOD__
            );
        }

        $this->registerAsyncPool($data['threads'] < 1 ? 2 : $data['threads']);

        static::$blocked = $data['services.blocked'];

        unset($data['threads'], $data['services.blocked']);
        $this->data = $data;
    }

    function run() : void
    {
        $taskDadata = new DadataAsyncTask('dadata');
        $taskLocalRest = new LocalRestAsyncTask('localhost');
        $taskRandom = new RandomDataAsyncTask('random');

        $taskDadata->setDescription('Dadata REST API Library');
        $taskDadata->write($this->data);

        $taskLocalRest->setDescription('Local REST API');
        $taskLocalRest->write($this->data, ['inn']);

        $taskRandom->setDescription('Random data');
        $taskRandom->write($this->data, ['inn']);

        if(!in_array('dadata', static::$blocked))
            $this->asyncPool->submit($taskDadata);
        if(!in_array('local', static::$blocked))
            $this->asyncPool->submit($taskLocalRest);
        if(!in_array('random', static::$blocked))
            $this->asyncPool->submit($taskRandom);
    }

    /**
     * <title>
     *  When task was completed
     * <title>
     *
     * @param $data
     * @param bool $unserialized
     *
     */
    function onComplete(&$data, bool $unserialized = true)
    {
        $this->asyncPool->onCompletion($data, $unserialized);
        $this->asyncPool->shutdown();
    }

    /**
     *
     * <title>
     *  AsyncPool registering
     * </title>
     *
     * @param int $threads
     *
     */
    private function registerAsyncPool(int $threads = 0) : void
    {
        $this->asyncPool = new AsyncPool($threads);
    }

    /**
     *
     * For errors handling
     *
     * @param string $file
     * @param string $message
     * @param string $function
     * @param string $type
     */
    function generateErrorMessage(string $file, string $message, string $function, string $type = 'Package Error') : void
    {
        // date string format 2000.01.1 00:00:00
        $date = date('d.m.Y h:i:s');
        $errorMessage = static::LOG_MESSAGE;

        rplc('{date}', $date, $errorMessage);
        rplc('{file}', $file, $errorMessage);
        rplc('{message}', $message, $errorMessage);
        rplc('{args}', implode(' ', $_SERVER['argv']), $errorMessage);
        rplc('{version}', PHP_VERSION, $errorMessage);
        rplc('{function}', $function, $errorMessage);
        rplc('{type}', $type, $errorMessage);

        file_put_contents($this->logFile, $errorMessage);
        exit(-1);
    }

    private $logFile = '';
    const LOG_MESSAGE = "Error {date}\n-------------------------------\nfile ----> {file}\n===============================\nType: {type}\nMessage: {message}\nFunction: {function}\n===============================\n\n==============Debug============\nArguments given: {args}\nPHP version: {version}";

}
