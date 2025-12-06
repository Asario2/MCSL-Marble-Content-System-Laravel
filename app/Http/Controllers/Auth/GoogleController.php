<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Settings;

class GoogleController extends Controller
{
    /**
     * Schritt 1: Weiterleitung zu Google
     * Mit Kontoauswahl (prompt=select_account)
     */
    public function redirectToGoogle()
    {
        $domain = request()->getHost(); // oder SD()

        // Socialite schon HIER dynamisch konfigurieren!
        config([
            'services.google.redirect' => "https://{$domain}/auth/google/callback",
        ]);

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->stateless()
            ->redirect();
    }

    /**
     * Schritt 2: Google Callback verarbeiten
     */
public function handleGoogleCallback()
{
    $domain = request()->getHost();

    config([
        'services.google.redirect' => "https://{$domain}/auth/google/callback",
    ]);

    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::where('email', $googleUser->email)->first();

    if (!$user) {
        $user = $this->createUserIfAllowed($googleUser);

        if (!$user) {
            \Log::error('Unknown email address', [
                'email' => $googleUser->email,
                'domain' => $domain,
            ]);

            return redirect()->route('login')
                ->withErrors(['login' => 'Email Adresse ungültig.']);
        }
    }

    // ❗ Email verifiziert setzen, weil Google verified liefert
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    Auth::login($user);

    // Nickname check
    if (!$user->name && @Settings::$regdom[SD()]) {
        return redirect()->route('auth.nickname');
    }

    return redirect()->route('home.index');
}


    private function createUserIfAllowed($googleUser)
    {
        // Registrierungs-Domain erlaubt?
        if (!@Settings::$regdom[SD()]) {
            return null; // Registrierung NICHT erlaubt
        }
        $randomString = substr(bin2hex(openssl_random_pseudo_bytes(16)), 0, 16);


        // Nutzer neu anlegen
        return User::create([
            'email'     => $googleUser->email,
            'google_id' => $googleUser->id,
            'name'      => null,
            "password"  => Hash::make($randomString),
        ]);
    }

    public function showNicknameForm()
    {
        if (!session()->has('oauth_temp')) {
            return redirect('/login')->with('error', 'Keine OAuth Daten vorhanden.');
        }

        return view('auth.nickname');
    }
    public function update(Request $request)
{
    $request->validate([
        'nickname' => 'required|min:3|max:30|unique:users,nickname,' . auth()->id(),

        'birthday' => 'nullable|date',
        'music' => 'nullable|string|max:255',
        'occupation' => 'nullable|string|max:255',
        'interests' => 'nullable|string|max:255',
        'fbd' => 'nullable|string|max:255',
        'about' => 'nullable|string',
        'headline' => 'nullable|string|max:255',
        'website' => 'nullable|string|max:255',
        'aufgabe' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:255',
    ]);

    auth()->user()->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Profil gespeichert',
    ], 200);
}

}
