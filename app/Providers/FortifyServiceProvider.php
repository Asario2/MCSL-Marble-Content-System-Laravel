<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\CustomLoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Pipeline\Pipeline;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ✅ DEIN Redirect-Fix
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
        $this->app->singleton(
            TwoFactorLoginResponse::class,
            CustomTwoFactorLoginResponse::class
        );
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())) . '|' . $request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->session()->get('login.id')
            );
        });
    }

    protected function loginPipeline(\Laravel\Fortify\Http\Requests\LoginRequest $request)
    {
        return (new Pipeline(app()))
            ->send($request)
            ->through(array_filter([
                config('fortify.limiters.login')
                    ? null
                    : \Laravel\Fortify\Http\Middleware\EnsureLoginIsNotThrottled::class,

                config('fortify.lowercase_usernames')
                    ? \Laravel\Fortify\Actions\CanonicalizeUsername::class
                    : null,

                \App\Actions\Auth\MigrateOldPassword::class,

                \Laravel\Fortify\Actions\AttemptToAuthenticate::class,
                \Laravel\Fortify\Actions\PrepareAuthenticatedSession::class,
            ]));
    }
}
