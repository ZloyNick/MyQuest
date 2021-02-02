<?php

/*
 *
 * This class submits and runs tasks.
 *
 * Also, class collects finished tasks.
 *
 */

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

use App\Lib\ZloyNick\pthreads\task\{
    DadataAsyncTask,
    LocalRestAsyncTask,
    RandomDataAsyncTask
};

use function in_array, array_push;

class Scheduler
{

    /**
     * @var string[] $blocked
     * <p>
     *  Site blocks services,
     *  where error has given
     *  5 times/hour.
     * </p>
     * @var AsyncPool $pool
     * @var string $data
     */
    private
        $asyncPool,
        $data = [],
        $blocked = [];


    /**
     *
     * <title>
     *  Creates Workers
     * </title>
     *
     * @param array $data
     * <p>
     *  Contains inn, threads, blocked services
     * </p>
     *
     * @return Scheduler
     *
     */
    function init(array $data = []) : Scheduler
    {
        $this->registerAsyncPool($data['threads'] < 1 ? 2 : $data['threads']);

        $this->blocked = $data['services.blocked'];
        unset($data['threads'], $data['services.blocked']);
        $this->data = $data;

        return $this;
    }

    /**
     *
     * <title>
     *  Runs tasks with different
     *  services using
     * </title>
     *
     * @return Scheduler
     *
     * @throws IncorrectValueException
     *
     */
    function run() : Scheduler
    {
        /** @var AsyncTask[] $arrayOfThreaded */
        $arrayOfThreaded = [];

        array_push(
            $arrayOfThreaded,
            (new DadataAsyncTask('dadata'))->write($data = $this->data),
            (new LocalRestAsyncTask('localhost'))->write($data),
            (new RandomDataAsyncTask('random'))->write($data)
        );

        $blockedServices = $this->blocked;

        foreach ($arrayOfThreaded as $task)
        {
            if(!in_array($task->getService(), $blockedServices))
            {
                $this->asyncPool->submit($task);
            }
        }

        return $this;
    }

    /**
     * <title>
     *  When tasks finished,
     *  unstacks Pool
     * <title>
     *
     * @param bool $unserialized
     *
     * @return array
     */
    function onComplete(bool $unserialized = true) : array
    {
        return $this->asyncPool->onCompletion($unserialized);
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

}
