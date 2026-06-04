<?php

namespace App\Console;

use App\Console\Commands\RunDataMining;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected array $commands = [
        RunDataMining::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('data-mining:run')->dailyAt('00:00')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
