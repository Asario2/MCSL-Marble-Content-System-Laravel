<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        // 🔁 Redirect aus Query (?redirect=...)
        if ($request->has('redirect')) {
            return redirect($request->get('redirect'));
        }

        // 🔁 Fallback: Laravel Standard
        return redirect()->intended(config('fortify.home'));
    }
}
