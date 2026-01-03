<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    // Alte + neue Passwortlogik
    public function authenticate_alt()
    {


    
        $loginInput = $this->input('email');
        $plainPassword = $this->input('password');
        $remember = $this->boolean('remember');

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $user = User::where($field, $loginInput)->first();

        if ($user) {
            // Neuer Hash prüfen
            if (Hash::check($plainPassword, $user->password)) {
                Auth::login($user, $remember);
                return;
            }

            // Altes Passwort prüfen
            if ($user->password_old) {
                $hasher = new \PasswordHash(8, true); // alte App-Logik
                if ($hasher->CheckPassword($plainPassword, $user->password_old)) {
                    $user->password = Hash::make($plainPassword);
                    $user->password_old = null;
                    $user->save();

                    Auth::login($user, $remember);
                    return;
                }
            }
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);

    }
}
