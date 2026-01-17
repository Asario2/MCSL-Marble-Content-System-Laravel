<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse
    {
        // 🔁 Redirect aus Query (?redirect=...)
        if ($request->has('redirect')) {
            return redirect()->to($request->get('redirect'));
        }

        // 🔁 Fallback: Laravel Standard
        return redirect()->intended(config('fortify.home'));
    }
}
