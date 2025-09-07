<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Illuminate\Support\Facades\Schema;
class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    // public function reset(User $user, array $input): void
    // {
    //     Validator::make($input, [
    //         'password' => $this->passwordRules(),
    //     ])->validate();

    //     $user->forceFill([
    //         'password' => Hash::make($input['password']),
    //     ])->save();
    // }
    public function reset(array $input)
    {
        Validator::make($input, [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ])->validate();

        $status = Password::reset(
            $input,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status;
    }

}
