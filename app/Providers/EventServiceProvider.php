<?php

namespace App\Providers;

 use Illuminate\Auth\Events\Registered;
use App\Listeners\SendAdminNewUserNotification;
use Illuminate\Auth\Events\Verified;
use App\Listeners\SetUserPubAfterVerification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */


protected $listen = [
    \Illuminate\Auth\Events\Registered::class => [
        \App\Listeners\SendAdminNewUserNotification::class,
    ],
];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
