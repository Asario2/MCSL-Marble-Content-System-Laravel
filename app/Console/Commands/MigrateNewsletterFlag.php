<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateNewsletterFlag extends Command
{
    /**
     * Artisan Command Name
     */
    protected $signature = 'migrate:newsletter-flag';

    /**
     * Beschreibung
     */
    protected $description = 'Verschiebt den Wert xch_newsletter von ffrog_users_config nach users.xch_newsletter';

    /**
     * Command ausführen
     */
    public function handle()
    {
        $this->info('Starte Migration der Newsletter-Werte...');

        // Prüfen, ob beide Tabellen existieren
        if (!DB::getSchemaBuilder()->hasTable('ffrog_users_config') || !DB::getSchemaBuilder()->hasTable('users')) {
            $this->error('Eine der Tabellen (ffrog_users_config oder users) existiert nicht!');
            return Command::FAILURE;
        }

        // Alle configs holen, die das Feld xch_newsletter besitzen
        $configs = DB::table('ffrog_users_config')
            ->select('users_id', 'xch_newsletter')
            ->whereNotNull('xch_newsletter')
            ->get();

        if ($configs->isEmpty()) {
            $this->info('Keine Newsletter-Werte zum Migrieren gefunden.');
            return Command::SUCCESS;
        }

        $count = 0;

        // Werte in users schreiben
        foreach ($configs as $conf) {
            DB::table('users')
                ->where('id', $conf->users_id)
                ->update(['xch_newsletter' => $conf->xch_newsletter]);
            $count++;
        }

        $this->info("Migration abgeschlossen: {$count} Einträge aktualisiert.");
        return Command::SUCCESS;
    }
}
