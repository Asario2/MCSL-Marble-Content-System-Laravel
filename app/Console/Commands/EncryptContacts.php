<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptContacts extends Command
{
    protected $signature = 'contacts:encrypt {--decrypt}';
    protected $description = 'Verschlüssele oder entschlüssele Kontakte-Felder mit APP_HASHCODE';

    public function handle()
    {
        $decrypt = $this->option('decrypt');

        $rows = DB::table('ene_kontakte')->get();
        $this->info("Gefundene Datensätze: " . $rows->count());

        foreach ($rows as $row) {
            $update = [];

            foreach ([
                'Vorname', 'Nachname', 'Email', 'Telefon',
                'Handy', 'Strasse', 'Plz', 'Geburtsdatum', 'Kommentar'
            ] as $field) {
                if ($decrypt) {
                    $update[$field] = decval($row->$field);
                } else {
                    $update[$field] = encval($row->$field);
                }
            }

            DB::table('ene_kontakte')->where('id', $row->id)->update($update);
        }

        $this->info($decrypt ? "Alle Kontakte entschlüsselt." : "Alle Kontakte verschlüsselt.");
        return 0;
    }
}
