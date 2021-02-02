<?php

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

use Threaded, Pool;

use function usleep;

class AsyncPool extends Pool
{

    /** @var int[] */
    private $tasks = [];// counter for highloads calculations
    /** @var AsyncWorker[] */
    protected $workers = [];// \Thread

    function __construct(int $size)
    {
        parent::__construct($size, AsyncWorker::class, []);
        for ($i = 0; $i < $size; $i++) {
            $this->workers[$i] = new AsyncWorker();
            $this->tasks[$i] = 0;
            $this->workers[$i]->start();
            $this->workers[$i]->run();
        }
    }

    /**
     * @param Threaded $task
     * @return int|void
     */
    function submit(Threaded $task)
    {
        $this->submitTo($this->getNextThreadId(), $task);
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
     *  Return thread's id, where load is minimum
     * </title>
     *
     * @return int
     *
     */
    function getNextThreadId(): int
    {
        $threadsLoad = &$this->tasks;
        $minLoad = 0;

        for ($i = 0; $i < $this->size; $i++) {
            if ($threadsLoad[$i] == 0)
                return $i;

            // line selector
            if ($threadsLoad[$i] < $threadsLoad[$minLoad])
                $minLoad = $threadsLoad[$i] < $threadsLoad[$minLoad] ? $i : $minLoad;
        }

        return $minLoad;
    }


    /**
     *
     * <title>
     *  All tasks completion procedure
     * </title>
     *
     * @param array $resultVar
     * <p>
     *  Result will be writen to array
     * </p>
     *
     */
    function onCompletion(array &$resultVar, bool $unserialized): void
    {

        /**
         * @var int $id
         * @var AsyncWorker $worker
         */
        foreach ($this->workers as $id => $worker) {
            /**
             * @var int $taskId
             * @var AsyncTask $task
             */
            foreach ($worker->getTasks() as $taskId => $task) {

                while (!$task->isCompleted()) ;

                usleep(100);

                $worker->collect(
                    function (AsyncTask $task) use (&$resultVar, $unserialized) {
                        $output = $task->getOutput($unserialized);
                        $resultVar[$s = $task->getService()] = ['companies' => [], 'status' => 500, 'message' => ''];
                        $resultVar[$s]['status'] = $status = isset($output['message']) ? 500 : 200;

                        if($status == 200)
                        {
                            $resultVar[$s]['companies'] = $output;
                            $resultVar[$s]['status'] = 200;
                        }else $resultVar[$s]['message'] = $output['message'];
                    }
                );

                $worker->removeTask($taskId);
                $this->tasks[$id]--;
            }

            // unstacking Worker. Now, load of worker with id $id is 0.
            $worker->unstack();
        }
    }

}
