<?php

namespace App\Console;

use App\Console\Commands\CheckUserDayCommand;
use App\Console\Commands\CheckUserPayCommand;
use App\Console\Commands\VerificationSubsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        VerificationSubsCommand::class,
        CheckUserPayCommand::class,
        CheckUserDayCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        try {
            $schedule->command('VerSud')
                ->everySixHours()
                ->timezone('Asia/Almaty');
            $schedule->command('CheckPay')
                ->hourly()
                ->timezone('Asia/Almaty');
            $schedule->command('CheckDay')
                ->dailyAt('09:00')
                ->timezone('Asia/Almaty');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

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
