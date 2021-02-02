<?php

/*
 *
 * Class for manipulations with
 * workers
 *
 */

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

use Threaded, Pool;

use function usleep;

class AsyncPool extends Pool
{

    /**
     * @var int[] $tasks
     * @var AsyncWorker[] $workers
     * @var int $nextWorkerId
     */
    protected
        $nextWorkerId = 0,
        $workers = [],
        $tasks = [];

    /**
     *
     * <title>
     *  Registers workers
     * </title>
     *
     * @param int $size
     */
    function __construct(int $size)
    {
        for ($i = 0; $i < $size; $i++) {
            $this->workers[$i] = new AsyncWorker();
            $worker = &$this->workers[$i];
            $worker->start();
            $worker->run();
            $this->tasks[$i] = 0;
        }

        parent::__construct($size, AsyncWorker::class, []);
    }

    /**
     * @param Threaded $task
     * @return int|void
     */
    function submit(Threaded $task)
    {
        $this->submitTo(
            $this->nextWorkerId == $this->size - 1
            ? 0 : $this->nextWorkerId++,
            $task
        );
    }

    /**
     *
     * <title>
     *  Running task at current thread
     * </title>
     *
     * @param int $worker
     * @param Threaded $task
     *
     */
    function submitTo(int $worker, Threaded $task): void
    {
        $this->workers[$worker]->stack($task);
        $this->tasks[$worker]++;
    }

    /**
     *
     * <title>
     *  All tasks completion procedure
     * </title>
     * @param bool $unserialized
     * @return array
     */
    function onCompletion(bool $unserialized) : array
    {
        $data = [];

        /**
         * @var int $id
         * @var AsyncWorker $worker
         */
        foreach ($this->workers as $id => $worker)
        {
            /**
             * @var int $taskId
             * @var AsyncTask $task
             */
            foreach ($worker->getTasks() as $taskId => $task)
            {
                while (!$task->isCompleted());

                usleep(100);

                $worker->collect(
                    function (AsyncTask $task) use (&$data, $unserialized)
                    {
                        $output = $task->getOutput($unserialized);
                        $data[$s = $task->getService()] = [
                            'companies' => $output,
                            'status' => ($message = $output['message']) ? 500 : 200,
                            'message' => $message
                        ];
                    }
                );

                $worker->removeTask($id);
                $this->tasks[$id]--;
            }

            $worker->unstack();
            $worker->kill();
        }

        return $data;
    }

}
