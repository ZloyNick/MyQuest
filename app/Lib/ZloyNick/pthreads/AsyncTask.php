<?php

/*
 *
 * Adapted class for all
 * tasks.
 *
 */

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

use Exception, Threaded, ComposerAutoloaderInit265cfa55c83f224b7f33bd3f36a008a6;

use function serialize, unserialize, is_null, preg_match;

class AsyncTask extends Threaded
{

    /**
     * @var bool $completed
     * @var string $output
     * @var string $service
     */
    private
        $completed = false,
        $output = '',
        $service = '';

    /**
     *
     * Service's name
     *
     * @param string $service
     */
    function __construct(string $service)
    {
        $this->service = $service;
    }

    /**
     * <title>
     *  Only for autoload
     * </title>
     */
    public function run() : void
    {
        require __DIR__ . '/../../../../vendor/composer/autoload_real.php';
        ComposerAutoloaderInit265cfa55c83f224b7f33bd3f36a008a6::getLoader();
    }

    /**
     *
     * <title>
     *  Array's values recording
     * </title>
     *
     * @param mixed[] $data
     * <p>
     *  An array of anything.
     *  <b>Exception:</b>
     *  <br>
     *  <font color='aqua'>
     *      the thread does not have access to the loaded classes and spaces of the main thread!
     *  </font>
     * </p>
     *
     * @return AsyncTask
     *
     * @throws IncorrectValueException
     * <p>
     *  Array key must only contain Latin and Arabic numerals and '_'
     * <p>
     *
     */
    public function write(array $data) : AsyncTask
    {
        /**
         *
         * Keys need to in validate
         * before recording (A-Za-z1-9_)
         *
         * @var mixed $key
         * @var mixed $value
         *
         */
        foreach ($data as $key => $value)
        {
            if (!preg_match('/^[_a-zA-Z0-9]+$/', $key))
            {
                throw new IncorrectValueException($key);
            }

            $this->{$key} = serialize($value);
        }

        return $this;
    }

    /**
     *
     * <title>
     *  Получаем значение переменной
     * </title>
     *
     * @param string $key
     * <p>
     *  Искомое значение
     * </p>
     *
     * @return mixed
     *
     * @throws Exception
     *
     */
    protected function readValue(string $key)
    {
        if (is_null($v = $this->{$key}))
        {
            throw new Exception('Trying to get a non-existent variable');
        }

        return unserialize($v);
    }

    /**
     *
     * <title>
     *  Task's status
     * </title>
     *
     * @return bool
     *
     */
    function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     *
     * <title>
     *  Changing task's status
     * </title>
     *
     * @param bool $completed
     * <p>
     *  Required bool: true
     * </p>
     *
     */
    protected function setCompleted(bool $completed = true): void
    {
        $this->completed = $completed;
    }

    /**
     *
     * <title>
     *  Sets result of code execution
     * </title>
     *
     * @param string $serializedResult
     *
     */
    protected function setOutput(string $serializedResult)
    {
        $this->output = $serializedResult;
    }

    /**
     *
     * <title>
     *  Result of execution
     * </title>
     *
     * @param bool $unserialized
     *
     * @return mixed
     */
    function getOutput(bool $unserialized)
    {
        return $unserialized ? unserialize($this->output) : $this->output;
    }

    /**
     * <title>
     *  Service name
     * </title>
     *
     * @return string
     */
    function getService(): string
    {
        return $this->service;
    }

}
