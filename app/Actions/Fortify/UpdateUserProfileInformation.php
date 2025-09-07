<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        // Wenn $input ein Array ist, verwende es direkt
        $inputData = $input;

        // Merge birthday field
        $inputData = array_merge($inputData, [
            'birthday' => $inputData['birthday'] ?? null, // Verwende den Null-Coalescing-Operator
        ]);

        // Validierung

            Validator::make($inputData, [
                'first_name' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
                'interests' => ['nullable', 'string', 'max:255'],
                'music' => ['nullable', 'string', 'max:255'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'headline' => ['nullable', 'string', 'max:255'],
                'aufgabe' => ['nullable', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],

            ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $table = "users"; // ermittelt die Tabelle des Models

            $filtered = collect($input)->filter(function ($value, $key) use ($table) {
                return Schema::hasColumn($table, $key);
            })->toArray();
            \Log::info($input);
            $user->forceFill(array_merge($filtered, [

                'updated_at' => now(),
            ]))->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $table = "users"; // ermittelt die Tabelle des Models

        $filtered = collect($input)->filter(function ($value, $key) use ($table) {
            return Schema::hasColumn($table, $key);
        })->toArray();

        $user->forceFill(array_merge($filtered, [

            'updated_at' => now(),
        ]))->save();

        $user->sendEmailVerificationNotification();
    }
}
