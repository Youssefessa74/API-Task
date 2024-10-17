<?php

namespace App\Console;

use App\Jobs\DeleteOldPosts;
use App\Jobs\FetchRandomUser;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         /* I Did It every minute to test it */
        // $schedule->job(new DeleteOldPosts())->everyMinute();
        // $schedule->job(new FetchRandomUser())->everyMinute();
        /* And This is what task demands for */
        $schedule->job(new DeleteOldPosts())->monthly();
        $schedule->job(new FetchRandomUser())->daily();
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
