<?php

/**
 * Status: Done!
 */

namespace Tests\Feature;

use Symfony\Component\Process\Process;
use Tests\TestCase;

class PthreadsAsyncRequest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return array
     */
    public function test_example($inn = 2310031475)
    {
        $threads = env('THREADS');
        $token = env('DADATA_TOKEN');
        $runtime = env('PTHREADS_PHP_RUNTIME');
        $scriptSrc = "/home/zloynick/test/public/scripts/AsyncCompanySearch.php";

        $this->assertTrue(file_exists($scriptSrc));

        $process = new Process([$runtime, $scriptSrc, $threads, $inn, $token, ""]);
        $process->run(function ($type, $buffer) {
            $this->assertNotTrue(Process::ERR === $type);
        });

        $data = unserialize($process->getOutput());

        $this->assertIsArray($data);

        foreach ($data as $k => $v)
        {
            $this->assertIsArray($v);
        }

        return $data;
    }
}
