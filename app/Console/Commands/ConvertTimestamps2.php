<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertTimestamps2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-timestamps2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Konvertiert birthday_ts und lastlogin_ts in formatiertes Datum';

    public function handle()
    {
        $table = 'oliver_rein.users'; // ← Passe das ggf. an deine Tabelle an

        $users = DB::table($table)->get();

        foreach ($users as $user) {
            $updates = [];

            if (!empty($user->birthday_ts)) {
                $updates['birthday'] = date('Y-m-d', $user->birthday_ts);
            }

            if (!empty($user->lastlogin_ts)) {
                $updates['last_login_at'] = date('Y-m-d H:i:s', $user->lastlogin_ts);
            }
            if (!empty($user->reg_date)) {
                $updates['created_at'] = date('Y-m-d H:i:s', $user->reg_date);
            }
            if (!empty($user->reg_date)) {
                $updates['updated_at'] = date('Y-m-d H:i:s', $user->reg_date);
            }

            if (!empty($updates)) {
                DB::table($table)
                    ->where('id', $user->id)
                    ->update($updates);

                $this->info("User ID {$user->id} aktualisiert: " . json_encode($updates));
            }
        }

        $this->info("Fertig. Alle Timestamps wurden konvertiert.");
        return 0;
    }

}
