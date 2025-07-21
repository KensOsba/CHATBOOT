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
        // Ejecutar solo el último día del mes a las 9 AM
        //$schedule->command('clientes:notificar-pago-internet')->monthlyOn(date('t'), '17:55');
     //   $schedule->command('clientes:notificar-pago-internet')->everyMinute();
    $schedule->command('clientes:notificar-pago-internet')->everyMinute();
   

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
