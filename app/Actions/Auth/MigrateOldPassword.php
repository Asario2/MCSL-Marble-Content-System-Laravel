<?php

namespace App\Actions\Auth;

use App\Support\PasswordHash;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MigrateOldPassword
{
    public function handle($request, \Closure $next)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && $user->password_old) {
            $legacy = new PasswordHash(8, true);

            if ($legacy->CheckPassword($request->password, $user->password_old)) {
//                 Log::info("Altes Passwort erfolgreich validiert → migriere!");

                $user->password = Hash::make($request->password);
                $user->password_old = null;
                $user->save();
            } else {
                Log::warning("Altes Passwort-Check fehlgeschlagen!");
            }
        }

        return $next($request);
    }
}
