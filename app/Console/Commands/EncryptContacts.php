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

        $rows = DB::table('asarios_BLog.private_messages_text')->get();
        $this->info("Gefundene Datensätze: " . $rows->count());

        foreach ($rows as $row) {
            $update = [];

            foreach ([
                'messagep'
            ] as $field) {
                if ($decrypt) {
                    $update[$field] = decval($row->$field);
                } else {
                    $update[$field] = encval($row->$field);
                }
            }

            DB::table('asarios_BLog.private_messages_text')->where('id', $row->id)->update($update);
        }

        $this->info($decrypt ? "Alle Kontakte entschlüsselt." : "Alle Kontakte verschlüsselt.");
        return 0;
    }
}
