<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Die Artisan-Befehle der Anwendung.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ConvertTimestamps::class,
        \App\Console\Commands\ResizeImages::class,
        \App\Console\Commands\MigrateComments::class,
        \App\Console\Commands\ApiRoutesCommand::class,
        \App\Console\Commands\FixImageJson::class,
        \App\Console\Commands\CheckBirthdays::class,



    ];

    /**
     * Definiere die Befehle der Anwendung.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Hier können geplante Tasks definiert werden
    }

    /**
     * Registriere die Befehle für die Anwendung.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
