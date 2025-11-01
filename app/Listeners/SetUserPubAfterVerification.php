<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;

class SetUserPubAfterVerification
{
    public function handle(Verified $event)
    {
        $user = $event->user;

        if ($user && ! $user->pub) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['pub' => 1]);
        }
    }
}
