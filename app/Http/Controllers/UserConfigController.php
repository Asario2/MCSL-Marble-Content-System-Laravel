<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConfig;
use Illuminate\Http\Request;

class UserConfigController extends Controller
{
    public static function updateConfig($userId)
    {
        $user = User::findOrFail($userId);

        UserConfig::updateOrCreate(
            ['users_id' => $user->id],
            ['pub'=>"1"],

        );

        // return redirect()->back()->with('success', 'UserConfig aktualisiert!');
    }
}
