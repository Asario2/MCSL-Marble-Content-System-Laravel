<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckPubColumn extends Command
{
    protected $signature = 'admin:check-pub';
    protected $description = 'Überprüft alle admin_table Einträge, ob deren Tabellen eine pub-Spalte besitzen';

    public function handle()
    {
        $db = "dagidag.";
        $rows = DB::table(''.$db.'admin_table')->pluck('name');

        if ($rows->isEmpty()) {
            $this->error("Keine Einträge in admin_table gefunden.");
            return Command::FAILURE;
        }

        $this->info("Prüfe Tabellen auf Spalte 'pub'...\n");

        foreach ($rows as $table) {

            if (!Schema::hasTable($db.$table)) {
                $this->warn("⚠ Tabelle '$table' existiert nicht.");
                continue;
            }

            if (Schema::hasColumn($db.$table, 'pub')) {
                $this->info("✔ $table enthält 'pub'");
            } else {
                $this->error("✖ $table enthält KEINE 'pub'");
            }
        }

        return Command::SUCCESS;
    }
}
