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
        $schedule->command('currency:fetch-rates')
            ->timezone('UTC') // Change to UTC
            ->dailyAt('08:00'); // 9 AM WAT is 8 AM UTC
    
        $schedule->command('currency:fetch-rates')
            ->timezone('UTC') // Change to UTC
            ->dailyAt('14:00') // 3 PM WAT is 2 PM UTC
            ->withoutOverlapping();
    

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
