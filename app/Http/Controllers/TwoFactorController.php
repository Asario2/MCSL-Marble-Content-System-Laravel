<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function enable(Request $request)
    {
        if (! $request->user()->two_factor_secret) {
            $request->user()->forceFill([
                'two_factor_secret' => encrypt(app('pragmarx.google2fa')->generateSecretKey()),
                'two_factor_enabled' => "1",
            ])->save();
        }

        return response()->noContent(); // Noch nicht aktiviert!
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $valid = app('pragmarx.google2fa')->verifyKey(
            decrypt($request->user()->two_factor_secret),
            $request->code
        );

        if (! $valid) {
            return back()->withErrors(['code' => 'Der Code ist ungültig.']);
        }

        $request->user()->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_enabled' => "1",
        ])->save();

        return response()->noContent();
    }
    public function enableTwoFactorAuthentication(Request $request)
    {
        $user = $request->user();
        $user->enableTwoFactorAuthentication(); // Dies sollte die Methode aufrufen, die 2FA aktiviert.

        // Antwort mit dem QR-Code und weiteren Daten senden
        return response()->json(["qrCode"=>"test",
            'message' => 'Zwei-Faktor-Authentifizierung aktiviert',
        ]);
    }
}
