<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomLoginControllerLegacy extends Controller
{
    // Login-Formular anzeigen
    public function showLoginForm()
    {
        if ($request->filled('redirect')) {
        session(['url.intended' => $request->redirect]);
        }
        return view('auth.login'); // dein Login-Blade
    }

    // Login ausführen
    public function login(LoginRequest $request)
    {
        $request->authenticate_alt();

        $request->session()->regenerate();

        return redirect()->intended('/home');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
