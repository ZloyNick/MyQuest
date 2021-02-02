<?php

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads;

use Exception, Threaded;

use function serialize, unserialize, is_null, preg_match, count;

class AsyncTask extends Threaded
{

    private
        $completed = false,// task's status
        $output = '',// description
        $service = '',
        $description = 'No description given';// Task's description

    /**
     * AsyncTask constructor.
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
    public function run()
    {
        require __DIR__ . '/../../../../vendor/composer/autoload_real.php';
        \ComposerAutoloaderInit265cfa55c83f224b7f33bd3f36a008a6::getLoader();
    }

    /**
     *
     * <title>
     *  Data recording.
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
     * @param string[] $keys
     * <p>
     *  Selects only the specified keys. Leave blank if all values are required
     *  <br>
     *  Example:
     * <code>
     *  $task = new AsyncTask();
     *  $data = ['k1' => 123, 'foo' => 'bar', 'fruit' => 'banana'];
     *  $task->write($data, ['fruit', 'k1']);
     *  var_dump($task->fruit, $task->k1);// string(6) "banana", int(123)
     * </code>
     * </p>
     *
     * @throws IncorrectValueException <p>
     *  Array key must only contain Latin and Arabic numerals and '_'
     * <p>
     *
     */
    public function write(array $data, array $keys = []): void
    {
        /**
         *
         * Keys need to be validated before recording (A-Za-z1-9_)
         *
         * @var mixed $k
         * @var mixed $v
         */
        foreach ($data as $key => $value) {
            if (!preg_match('/^[_a-zA-Z0-9]+$/', $key)) {
                throw new IncorrectValueException($key);
            }
        }

        // if array is not empty
        if (count($keys)) {
            foreach ($keys as $key) {

                if (!isset($data[$key])) {
                    throw new Exception('Trying to get missing key: ' . $key);
                }

                $this->{$key} = serialize($data[$key]);
            }

            // stop execution
            return;
        }

        foreach ($data as $k => $v) {
            $this->{$k} = serialize($v);
        }

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
        if (is_null($v = $this->{$key})) {
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
     *
     * <title>
     *  Sets result of code execution
     * </title>
     *
     * @param string $serializedResult
     *
     */
    function setDescription(string $text)
    {
        $this->description = $text;
    }

    /**
     *
     * <title>
     *  Result of execution
     * </title>
     *
     * @return string
     */
    function getDescription(): string
    {
        return $this->description;
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
