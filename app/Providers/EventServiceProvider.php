<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Verified;
use App\Listeners\SendAdminNewUserNotification;
use App\Listeners\SetUserPubAfterVerification;
use App\Listeners\CreateUserConfigIfMissing;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendAdminNewUserNotification::class,
            CreateUserConfigIfMissing::class,
        ],

        Login::class => [
            CreateUserConfigIfMissing::class,
        ],

        Verified::class => [
            SetUserPubAfterVerification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
