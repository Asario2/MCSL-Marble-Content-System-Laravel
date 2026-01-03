<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Contracts\LoginResponse;

class CustomLoginController extends Controller
{
    public function showLoginForm()
    {
        if ($request->filled('redirect')) {
        session(['url.intended' => $request->redirect]);
        }
        return view('auth.custom-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        if ($request->filled('redirect')) {
        return redirect($request->input('redirect'));
    }
        return app(\Illuminate\Pipeline\Pipeline::class)
            ->send($request)
            ->through([
                AttemptToAuthenticate::class,
                RedirectIfTwoFactorAuthenticatable::class,
                PrepareAuthenticatedSession::class,
            ])
            ->then(fn ($request) => app(LoginResponse::class));
    }

    public function showTwoFactorForm()
    {
        return view('auth.two-factor-challenge');
    }

    public function twoFactorLogin(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        return app(\Illuminate\Pipeline\Pipeline::class)
            ->send($request)
            ->through(config('fortify.pipelines.two-factor', [
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]))
            ->then(fn ($request) => app(TwoFactorLoginResponse::class));
    }
}
