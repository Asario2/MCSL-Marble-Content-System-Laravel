<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
class PersonalController
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(Request $request)
    {
        $user = auth()->user(); // oder direkt injizieren, wenn Route z. B. /user/{user} nutzt

        \Log::info("RA".json_encode($request->all()));
        $dateString = $request->input('birthday'); // z.B. '31.12.2023'

        // \Log::info('Raw birthday input: ' . $dateString);

        $request['birthday'] = date("Y-m-d 00:00:00",strtotime($dateString));
        $validated = $request->validate([
            'birthday' => ['nullable', 'date', 'before:today'],
            'music' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'interests' => ['nullable', 'string', 'max:255'],
            'fbd'=>['nullable','string','max:200'],
            'aufgabe'=>['nullable','string','max:200'],
            'location'=>['nullable','string','max:200'],
            'headline'=>['nullable','string','max:200'],
            'xch_newsletter' => ['nullable', 'boolean'],
        ], [], [], 'updateProfileInformation');

        \Log::info("✅ Empfangen:", $validated);

        $table = "users"; // ermittelt die Tabelle des Models

        $filtered = collect($validated)->filter(function ($value, $key) use ($table) {
            return Schema::hasColumn($table, $key);
        })->toArray();

        $user->forceFill(array_merge($filtered, [

            'updated_at' => now(),
        ]))->save();
    return response()->json(["success"=>true,"Messsage"=>"Profil gespeichert"]);
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
