<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Zeigt das Login-Formular an.
     */
    public function create()
    {
       return Inertia::render('Auth/Login');
    }

    /**
     * Führt den Login aus.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        dd('store called');

        $request->authenticate_alt();

        $request->session()->regenerate();

        return redirect()->intended('/home');
    }

    /**
     * Führt den Logout aus.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
