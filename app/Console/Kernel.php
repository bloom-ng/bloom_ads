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
            ->timezone('Africa/Lagos')
            ->dailyAt('09:00');

        $schedule->command('currency:fetch-rates')
            ->timezone('Africa/Lagos')
            ->dailyAt('15:00')
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
