<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(
            function ()
            {
                $redisClient = Redis::connection()->client();
                $redisClient->select("0");

                $redisClient->unlink(
                    'service:localhost:fails',
                    'service:localhost:error'
                );
                $redisClient->unlink(
                    'service:dadata:fails',
                    'service:dadata:error'
                );
                $redisClient->unlink(
                    'service:random:fails',
                    'service:random:error'
                );

                $redisClient->unlink(
                    'service:blocked'
                );

            }
        )->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
