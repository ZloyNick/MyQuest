<?php

/*
 *
 * AsyncWorker for AsyncTasks
 *
 * no description given.
 *
 */

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

use Worker, Threaded;

use function count;

class AsyncWorker extends Worker
{

    /**
     * @var object
     */
    private $tasks = [];

    /**
     *
     * <title>
     *  Due to the declaration of the Stream class, I cannot change Threaded to AsyncTask
     * </title>
     *
     * @param Threaded|AsyncTask $work
     * @return int
     */
    function stack(Threaded $work): int
    {
        $this->tasks[] = $work;

        return parent::stack(
            $this->tasks[count($this->tasks)-1]
        );
    }

    /**
     *
     * <title>
     *  Returns all tasks
     * </title>
     *
     * @return object
     *
     */
    function getTasks(): object
    {
        return $this->tasks;
    }

    /**
     *
     * <title>
     *  Task getting by task's id
     * </title>
     *
     * @param int $taskId
     * @return AsyncTask|null
     */
    function getTask(int $taskId) : ?AsyncTask
    {
        return $this->tasks[$taskId];
    }

    /**
     * @param int $id
     */
    function removeTask(int $id) : void
    {
        unset($this->tasks[$id]);
    }


    /**
     *
     * <title>
     *  Killing thread...
     * </title>
     *
     */
    function kill()
    {
        /**
         * @var int $taskId
         * @var AsyncTask $task
         */
        foreach ($this->tasks as $taskId => $task)
        {
            while (!$task->isCompleted()){ usleep(1000); };
        }
        parent::shutdown();
    }

    /**
     *
     * <title>
     *  <b color='red'>Use 'kill()' method</b>
     * <title>
     * @deprecated
     */
    function shutdown(){}

}
