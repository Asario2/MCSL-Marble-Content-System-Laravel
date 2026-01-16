<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string'], // kann E-Mail oder Nickname sein
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate()
    {
        //$this->ensureIsNotRateLimited();

        dd('authenticate aufgerufen');


        $loginInput = $this->input('email'); // oder nickname
        $plainPassword = $this->input('password');
        $remember = $this->boolean('remember');

        // Prüfen ob Eingabe eine E-Mail ist oder Nickname
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // User anhand Feld laden
        $user = \App\Models\User::where($field, $loginInput)->first();
        if ($user) {
//             \Log::info('User gefunden: ' . $user->id);
//             \Log::info('Altes Passwort: ' . $user->password_old);
        }
        if ($user) {
            // 1) Neuer Hash prüfen
            if (\Hash::check($plainPassword, $user->password)) {
                Auth::login($user, $remember);

                RateLimiter::clear($this->throttleKey());

                return;
            }

            // 2) Altes Passwort prüfen mit PasswordHash
            if ($user->password_old) {

                $hasher = new PasswordHash(8, true); // Parameter wie in alter App
                if ($hasher->CheckPassword($plainPassword, $user->password_old)) {
                    // ✅ migrieren
                    $user->password = \Hash::make($plainPassword);
                    $user->password_old = null;
                    $user->save();

                    Auth::login($user, $remember);

                    RateLimiter::clear($this->throttleKey());
                    return;
                }
            }
        }

        // 3) Alles fehlgeschlagen → Throttle + Fehler
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
        }


    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
