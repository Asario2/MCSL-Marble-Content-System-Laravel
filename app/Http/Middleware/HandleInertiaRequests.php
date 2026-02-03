<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HandleInertiaRequests extends Middleware
{
    /**
     * Root template für Inertia
     */
    protected $rootView = 'app';

    /**
     * Asset version
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Shared props für Inertia
     */
    public function share(Request $request): array
    {
        $user = Auth::user();

        return array_merge(parent::share($request), [
            'toast' => [
                'success' => $request->session()->get('toast.success'),
                'warning' => $request->session()->get('toast.warning'),
                'info' => $request->session()->get('toast.info'),
                'error' => $request->session()->get('toast.error'),
            ],

            'saas_url' => config('app.url'),

            'version' => [
                'brandname' => config('starter_eleven.version.brandname'),
                'copyrightname' => config('starter_eleven.version.copyrightname'),
                'versionnr' => config('starter_eleven.version.versionnr'),
                'versionsdatum' => config('starter_eleven.version.versionsdatum'),
            ],

            'applications' => [
                'app_admin' => 'admin',
                'app_employee' => 'employee',
                'app_customer' => 'customer',
                'app_central_name' => 'Zentrale',
                'app_admin_name' => 'Administrator-Anwendung',
                'app_employee_name' => 'Mitarbeiter-Anwendung',
                'app_customer_name' => 'Kunden-Anwendung',
                'app_name' => '<div class="flex justify-center items-center gap-2 text-sm text-layout-sun-700 dark:text-layout-night-700">
                                <a href="https://marblefx.net/powered-by-mcsl" onclick="event.stopPropagation();">
                                <span>Powered by</span>
                                <img src="/images/smilies/icon_mcsl.gif" alt="MCSL Icon" class="inline-block mt-1   align-middle" />
                                </a>
                                </div>',
                'brand_name_1' => 'Asarios',
                'brand_name_2' => 'Blog',
                'brand_name_subtitle_app_admin' => 'für Administratoren',
                'brand_name_subtitle_app_employee' => 'für Mitarbeiter',
                'brand_name_subtitle_app_customer' => 'für Kunden',
            ],

            'userdata' => function () use ($user) {
                if (!$user) {
                    return [
                        'user_id' => null,
                        'full_name' => null,
                        'profile_photo_path' => null,
                        'profile_photo_url' => null,
                        'is_admin' => false,
                        'is_employee' => false,
                        'is_customer' => false,
                        'application_count' => 0,
                    ];
                }

                $application_count = 0;
                $is_admin = $user->is_admin;
                $is_employee = $user->is_employee;
                $is_customer = $user->is_customer;

                if ($is_admin) $application_count++;
                if ($is_employee) $application_count++;
                if ($is_customer) $application_count++;

                return [
                    'user_id' => $user->id,
                    'full_name' => $user->full_name,
                    'profile_photo_path' => $user->profile_photo_path,
                    'profile_photo_url' => str_replace('public/', '', $user->profile_photo_url),
                    'is_admin' => $is_admin,
                    'is_employee' => $is_employee,
                    'is_customer' => $is_customer,
                    'application_count' => $application_count,
                ];
            },
        ]);
    }

    /**
     * Override root handle für Gäste → kein Redirect-Loop
     */
    public function rootView(Request $request)
    {
        if (!Auth::check() && $request->header('X-Inertia')) {
            // Gäste bekommen Login gerendert
            return Inertia::render('Auth/Login')->toResponse($request);
        }

        return parent::rootView($request);
    }
}
