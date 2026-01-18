<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateUserConfigIfMissing
{
    /**
     * Handle the event.
     *
     * The $event may be a Registered or a Login event (both have ->user).
     */
    public function handle($event)
    {
        // defensive check: hat das Event ein user-Objekt?
      if (! $event->user || ! $event->user->id) {
            Log::warning('CreateUserConfigIfMissing: Event enthält keinen user', ['event' => get_class($event)]);
            return;
        }

        $userId = $event->user->id;

//         Log::info('CreateUserConfigIfMissing läuft', ['user_id' => $userId, 'event' => get_class($event)]);

        // Prüfen ob schon ein Eintrag existiert
        $exists = DB::table('users_config')->where('users_id', $userId)->exists();

        if ($exists) {
//             Log::info('users_config Eintrag existiert bereits', ['user_id' => $userId]);
            return;
        }

        // Insert mit Standard-Werten
        try {
            DB::table('users_config')->insert([
                'users_id'          => $userId,
                'xch_newsletter'    => '0',
                'xis_pmtoautomail'  => 0,
                'cnt_numrows'       => 20,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

//             Log::info('users_config Eintrag erstellt', ['user_id' => $userId]);
        } catch (\Throwable $e) {
            Log::error('users_config Insert fehlgeschlagen', [
                'user_id' => $userId,
                'error'   => $e->getMessage()
            ]);
        }
    }
}
