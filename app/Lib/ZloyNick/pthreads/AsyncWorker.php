<?php


namespace App\Lib\ZloyNick\pthreads;

use Worker, Threaded, InvalidArgumentException;

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
        // checking class
        if(!$work instanceof AsyncTask)
        {
            throw new InvalidArgumentException(
                'Argument 1 passed as \Threaded, but argument 1 must be instance of ' .
                AsyncTask::class
            );
        }

        $this->tasks[] = $work;

        // Next task's id will equal count of tasks
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
