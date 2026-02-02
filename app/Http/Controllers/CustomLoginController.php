<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use App\Support\PasswordHash;
use App\Http\Controllers\UserConfigController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class CustomLoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($request->filled('redirect')) {
        session(['url.intended' => $request->redirect]);
        }
        // return "ASDd";
        //   //Inertia::render('Auth/Login');
        return Inertia::render('Auth/Login');

    }
    public function loginSilent(Request $request)
    {
    \Log::info('LOGIN-SILENT HIT');
    if(empty($request->password))
    {
        return response()->json([
        'type'=>'info',
        'message' => 'Kein Login möglich',
        'user_id' => 7,
        "full_name"=>"Gast",
        "profile_photo_url"=>"008.jpg"
    ]);
    }
    $credentials = $request->only('email', 'password');

    if (!$this->login($request,true)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    if(Auth::id() && Auth::id() != 7)
    {
    return response()->json([
        'type'=>'success',
        'message' => 'Sie wurden erfolgreich eingeloggt',
        'user_id' => Auth::id(),
        "full_name"=>Auth::user()->first_name ?? "Gast",
        "profile_photo_url"=>Auth::user()->profile_photo_path ?? "008.jpg",
    ]);
    }
    }
    public function login(Request $request,$silent=false)
    {
        // dd(class_exists(\Illuminate\Support\Facades\Validator::class));
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('email');
        $plainPassword = $request->input('password');
        $remember = $request->boolean('remember');

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $user = \App\Models\User::where($field, $loginInput)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Benutzer nicht gefunden.']);
        }

        // Neues Passwort prüfen
        if (!Hash::check($plainPassword, $user->password)) {
            // Altes Passwort prüfen
            if ($user->password_old) {
                $hasher = new PasswordHash(8, true);
                if ($hasher->CheckPassword($plainPassword, $user->password_old)) {
                    $user->password = Hash::make($plainPassword);
                    $user->password_old = null;
                    $user->save();
                } else {
                    return back()->withErrors(['password' => 'Falsches Passwort.']);
                }
            } else {
                return back()->withErrors(['password' => 'Falsches Passwort.']);
            }
        }


        // Wenn 2FA aktiv, in Session speichern und weiterleiten
        if ($user->two_factor_secret && $user->two_factor_enabled && !$silent) {
            session([
                'two_factor:user:id' => $user->id,
                'two_factor:remember' => $remember,
            ]);
            return redirect()->route('two-factor.login');
        }

        // Kein 2FA → direkt einloggen
        Auth::login($user, $remember);

        UserConfigController::updateConfig(Auth::id());


        $redirect = $request->input('redirect'); // POST oder GET
        if ($redirect && !$silent) {
            return Inertia::location($redirect . '?re=1');
        }
        if($silent)
        {
            return true;
        }

        return app(LoginResponse::class);
    }
    public function enableTwoFactor(Request $request)
    {
        $user = $request->user();

        $user->two_factor_enabled = 1;
        $user->save();

        return response()->json(['success' => true]);
    }
    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();

        $user->two_factor_enabled = 0;
        $user->save();

        return response()->json(['success' => true]);
    }
    public function showTwoFactorForm()
    {
        if (!session()->has('two_factor:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Bitte zuerst E-Mail und Passwort eingeben.'
            ]);
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    public function twoFactorLogin(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        if (!session()->has('two_factor:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Bitte zuerst E-Mail und Passwort eingeben.'
            ]);
        }

        $user = \App\Models\User::find(session('two_factor:user:id'));
        $remember = session('two_factor:remember', false);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Benutzer nicht gefunden.']);
        }

        // OTP oder Recovery Code prüfen
        if ($request->filled('code') && $user->verifyTwoFactorCode($request->code)) {
            // korrekt
        } elseif ($request->filled('recovery_code') && $user->verifyRecoveryCode($request->recovery_code)) {
            // korrekt
        } else {
            return back()->withErrors(['code' => 'Ungültiger Zwei-Faktor-Code.']);
        }

        // 2FA bestanden → final einloggen
        Auth::login($user, $remember);

        session()->forget(['two_factor:user:id', 'two_factor:remember']);

        return app(TwoFactorLoginResponse::class);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // dd("test");
        // return redirect('/')
        // ->with('force_reload', true);
        // return response()->json("true");
        return redirect()->route('home.rindex')
        ->with('needsReload', true);
    }
public function pw_recovery(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'current_password' => 'required|string',
        'new_password' => 'required|string|confirmed|min:8',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'Benutzer nicht gefunden.']);
    }

    $currentPassword = $request->input('current_password');

    // Prüfen: neues Laravel Passwort
    if (!\Illuminate\Support\Facades\Hash::check($currentPassword, $user->password)) {
        // Prüfen: altes Passwort
        if ($user->password_old) {
            $hasher = new \App\Support\PasswordHash(8, true); // alte App-Logik
            if (!$hasher->CheckPassword($currentPassword, $user->password_old)) {
                return back()->withErrors(['current_password' => 'Falsches Passwort.']);
            }
        } else {
            // return back()->withErrors(['current_password' => 'Falsches Passwort.']);
        }
    }

    // Neues Passwort **explizit hashen**
    // $user->password = \Illuminate\Support\Facades\Hash::make($request->input('new_password'));
    // $user->password_old = null;
    // $user->save();
    $user->fill([
        'password' => $request->input('new_password'), // 'hashed'-Cast greift hier
        'password_old' => null,
    ])->save();

    return back()->with('success', 'Passwort erfolgreich geändert.');
}

}
