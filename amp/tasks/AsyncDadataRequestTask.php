<?php

namespace amp\tasks;

use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;

class AsyncDadataRequestTask implements Task
{

    private $inn = '';

    public function __construct(string $inn)
    {
        $this->inn = $inn;
    }

    public function run(Environment $environment)
    {

    }
}
