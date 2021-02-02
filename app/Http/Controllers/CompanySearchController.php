<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Symfony\Component\Process\Process;
use Illuminate\Http\Request;

use function getcwd, unserialize, response;

use Exception;

class CompanySearchController extends Controller
{

    /**
     *
     * <p>
     *  Returns companies with given "INN"
     *  Only for axios.
     * </p>
     *
     * <p> Example </p>
     * <code>
     *      this.axios.post(
     *          "/href",
     *          [
     *              inn: this.inn
     *          ]
     *      ).then(res () => {
     *           //etc...
     *      })
     * </code>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function search(Request $request)
    {
        $inn = str_replace(' ', null, $request->offsetGet('inn'));
        $threads = env('THREADS');
        $token = env('DADATA_TOKEN');
        $runtime = env('PTHREADS_PHP_RUNTIME');
        $scriptSrc = getcwd().'/scripts/AsyncCompanySearch.php';

        $redisClient = Redis::connection()->client();
        $redisClient->select("0");

        $blockedServices = $redisClient->get('service:blocked');

        if(!$blockedServices)
        {
            $redisClient->set('service:blocked', "");
            $blockedServices = "";
        }

        $process = new Process([$runtime, $scriptSrc, $threads, $inn, $token, $blockedServices]);

        $data = $this->runProcess($process);
        $this->redisFill($data);

        return response()->json($data);
    }

    private function runProcess(Process &$process) : array
    {
        $process->run();
        $process->wait(function ($type, $buffer) {
            if (Process::ERR === $type) {
                throw new Exception($buffer);
            }
        });

        return unserialize($process->getOutput());
    }

    private function redisFill(array &$data) : void
    {
        $redisClient = Redis::connection()->client();
        $redisClient->select("0");

        foreach ($data as $service => $result)
        {
            if(!$redisClient->get('service:'.$service.':fails'))
                $redisClient->set('service:'.$service.':fails', 0);

            if($result['status'] == 500)
            {
                $redisClient->incr('service:'.$service.':fails');
                $redisClient->set('service:'.$service.':error', $result['message']);

                if($redisClient->get('service:'.$service.':fails') == 5)
                {
                    $redisClient->set(
                        'service:blocked',
                        $redisClient->get('service:blocked').','.$service
                    );
                }

                unset($data[$service]);
            }
        }
    }

}
