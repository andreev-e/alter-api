<?php

namespace App\Console;

use App\Console\Commands\DayReset;
use App\Console\Commands\FillLocations;
use App\Console\Commands\FillUrls;
use App\Console\Commands\MonthReset;
use App\Console\Commands\RegionsProcess;
use App\Console\Commands\TagCount;
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
        $schedule->command(FillUrls::class)->daily();
        $schedule->command(FillLocations::class)->daily();
        $schedule->command(RegionsProcess::class)->daily();
        $schedule->command(TagCount::class)->daily();

        $schedule->command(MonthReset::class)->monthly();
        $schedule->command(DayReset::class)->daily();

        $schedule->command('telescope:prune')->daily();
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
