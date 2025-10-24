<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EmailHash extends Command
{
    /**
     * Der Konsolenbefehl (Artisan name)
     */
    protected $signature = 'email:hash';

    /**
     * Beschreibung (wird in `php artisan list` angezeigt)
     */
    protected $description = 'Erstellt Hashes für alle E-Mail-Adressen in der contacts-Tabelle';

    public function handle()
    {
        $contacts = DB::table('oliver_rein.contacts')->get();
        $this->info("Verarbeite {$contacts->count()} Kontakte...");

        foreach ($contacts as $contact) {
            if (empty($contact->Email)) {
                continue;
            }

            try {
                $hash = hash('sha256', trim(decval($contact->Email)));
                \Log::info("hash: ".$hash);
                DB::table('oliver_rein.contacts')
                    ->where('id', $contact->id)
                    ->update(['email_hash' => $hash]);

                $this->line("✅ {$contact->Email} → {$hash}");
            } catch (\Exception $e) {
                $this->error("Fehler bei ID {$contact->id}: " . $e->getMessage());
            }
        }

        $this->info('Alle E-Mail-Hashes wurden erfolgreich generiert.');
        return Command::SUCCESS;
    }
}
