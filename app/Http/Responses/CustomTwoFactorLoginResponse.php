<?php
class CustomTwoFactorLoginResponse implements TwoFactorLoginResponse
{
    public function toResponse($request)
    {
        if ($request->has('redirect')) {
            return redirect($request->get('redirect'));
        }

        return redirect()->intended(config('fortify.home'));
    }
}
