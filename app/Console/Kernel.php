<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DailyReport',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
            //     ->everyFiveMinutes();
        // $schedule->command('generate:ba_report')->dailyAt('19:00');
        // $schedule->command('email:ba_report')->dailyAt('21:00');

        $schedule->command('generate:ba_report')->dailyAt('21:00');
        $schedule->command('email:ba_report')->dailyAt('23:59');

        $schedule->command('calculate:sku_count')->dailyAt('01:15');
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
