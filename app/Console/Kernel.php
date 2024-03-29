<?php

namespace App\Console;

use App\Console\Commands\DayReset;
use App\Console\Commands\DeleteLocalThubms;
use App\Console\Commands\DominateColor;
use App\Console\Commands\FillLocations;
use App\Console\Commands\FillUrls;
use App\Console\Commands\GenerateSitemap;
use App\Console\Commands\MonthReset;
use App\Console\Commands\RegionsProcess;
use App\Console\Commands\CountStats;
use App\Console\Commands\SetMediaWidthHeight;
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
        $schedule->command(DeleteLocalThubms::class)->hourly();
        $schedule->command(DominateColor::class)->hourly();

        $schedule->command(DayReset::class)->dailyAt('00:00');
        $schedule->command(FillUrls::class)->dailyAt('00:10');
        $schedule->command(FillLocations::class)->dailyAt('00:20');
        $schedule->command(RegionsProcess::class)->dailyAt('00:25');
        $schedule->command(CountStats::class)->dailyAt('00:30');
        $schedule->command('telescope:prune')->daily();

        $schedule->command(MonthReset::class)->monthly();

        $schedule->command(GenerateSitemap::class)->dailyAt('1:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
