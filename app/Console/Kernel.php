<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Orchestra\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\Com\ScanComputers::class,
        Commands\ActiveDirectory\SyncUsers::class,
        Commands\ActiveDirectory\SyncComputers::class,
        Commands\ClearMonthlyData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Scan computers every five minutes.
        $schedule->command('computers:scan')->everyFiveMinutes();

        // Clear computer records older than one month.
        $schedule->command('computers:clear-monthly')->everyTenMinutes();

        // Synchronize LDAP users.
        $schedule->command('users:sync')->hourly();

        // Synchronize LDAP computers.
        $schedule->command('computers:sync')->hourly();
    }
}
