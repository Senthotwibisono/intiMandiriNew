<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('functions:daily')->everyMinute();
        // $schedule->command('functions:cfsScheduler')->everyMinute();
        $schedule->command('functions:daily')->everyThreeMinutes();
        $schedule->command('functions:cfsScheduler')->everyThreeMinutes();
        $schedule->command('functions:gateInJict')->everyThreeMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
