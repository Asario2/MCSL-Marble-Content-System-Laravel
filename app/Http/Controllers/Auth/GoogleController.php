<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Schritt 1: Weiterleitung zu Google
     */
    public function redirectToGoogle()
    {
        $domain = request()->getHost();

        config([
            'services.google.redirect' => "https://{$domain}/auth/google/callback",
        ]);

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect(); // ❗ KEIN response(), KEIN stateless()
    }

    /**
     * Schritt 2: Callback von Google
     */
    public function handleGoogleCallback()
    {
        $domain = request()->getHost();

        config([
            'services.google.redirect' => "https://{$domain}/auth/google/callback",
        ]);

        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = $this->createUserIfAllowed($googleUser);

            if (!$user) {
                \Log::warning('Google Login abgelehnt (Domain nicht erlaubt)', [
                    'email' => $googleUser->email,
                    'domain' => $domain,
                ]);

                return redirect()
                    ->route('login')
                    ->withErrors(['login' => 'Email-Adresse nicht erlaubt.']);
            }
        }

        // Email als verifiziert markieren
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, true);

        // Nickname fehlt?
        if (!$user->name && !empty(Settings::$regdom[SD()] ?? null)) {
            return redirect()->route('auth.nickname');
        }

        return redirect()->route('home.index');
    }

    /**
     * User anlegen, falls erlaubt
     */
    private function createUserIfAllowed($googleUser): ?User
    {
        if (empty(Settings::$regdom[SD()] ?? null)) {
            return null;
        }

        return User::create([
            'email'     => $googleUser->email,
            'google_id' => $googleUser->id,
            'name'      => null,
            'password'  => Hash::make(Str::random(32)),
        ]);
    }

    /**
     * Nickname Formular anzeigen
     */
    public function showNicknameForm()
    {
        return view('auth.nickname');
    }

    /**
     * Nickname / Profil speichern
     */
    public function update(Request $request)
    {
        $request->validate([
            'nickname'   => 'required|min:3|max:30|unique:users,nickname,' . auth()->id(),
            'birthday'   => 'nullable|date',
            'music'      => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'interests'  => 'nullable|string|max:255',
            'fbd'        => 'nullable|string|max:255',
            'about'      => 'nullable|string',
            'headline'   => 'nullable|string|max:255',
            'website'    => 'nullable|string|max:255',
            'aufgabe'    => 'nullable|string|max:255',
            'location'   => 'nullable|string|max:255',
        ]);

        auth()->user()->update($request->only([
            'nickname',
            'birthday',
            'music',
            'occupation',
            'interests',
            'fbd',
            'about',
            'headline',
            'website',
            'aufgabe',
            'location',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profil gespeichert',
        ]);
    }
}
