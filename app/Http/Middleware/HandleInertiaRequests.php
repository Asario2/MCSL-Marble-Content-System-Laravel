<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'toast' => function () use ($request) {
                return [
                    'success' => $request->session()->get('toast.success'),
                    'warning' => $request->session()->get('toast.warning'),
                    'info' => $request->session()->get('toast.info'),
                    'error' => $request->session()->get('toast.error'),
                ];
            },
            // saas_url
            'saas_url' => function () {
                return "http://localhost/";
                // return config('app.url');
            },
            // version
            'version' => function () {
                return [
                    'brandname' => config('starter_eleven.version.brandname'),
                    'copyrightname' => config('starter_eleven.version.copyrightname'),
                    'versionnr' => config('starter_eleven.version.versionnr'),
                    'versionsdatum' => config('starter_eleven.version.versionsdatum'),
                ];
            },
            // applications
            'applications' => function () {
                return [
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
                                    <img src="/images/smilies/icon_mcsl.gif" alt="MCSL Icon" class="inline-block mt-2 align-middle" />
                                    </a>
                                    </div>',
                    'brand_name_1' => 'Asarios',
                    'brand_name_2' => 'Blog',
                    'brand_name_subtitle_app_admin' => 'für Administratoren',
                    'brand_name_subtitle_app_employee' => 'für Mitarbeiter',
                    'brand_name_subtitle_app_customer' => 'für Kunden',
                ];
            },
            // Userdaten
            'userdata' => function () {
                //
                $user_id = null;
                //
                $profile_photo_path = null;
                $profile_photo_url = null;
                //
                $is_admin = false;
                $is_employee = false;
                $is_customer = false;
                //
                $full_name = null;
                //
                $application_count = 0;
                //
                $user = Auth::user();
                //
                if ($user) {
                    $user_id = $user->id;
                    //
                    $full_name = $user->full_name;
                    //
                    $profile_photo_path = $user->profile_photo_path;
                    $profile_photo_url = str_replace("public/","",$user->profile_photo_url);
                    //
                    $is_admin = $user->is_admin;
                    $is_employee = $user->is_employee;
                    $is_customer = $user->is_customer;
                    // ==========================
                    // ermittle application_count
                    // ==========================
                    if ($is_admin) {
                        $application_count += 1;
                    }
                    if ($is_employee) {
                        $application_count += 1;
                    }
                    if ($is_customer) {
                        $application_count += 1;
                    }
                }
                //
                return [
                    'user_id' => $user_id,
                    'full_name' => $full_name,
                    //
                    'profile_photo_path' => $profile_photo_path,
                    'profile_photo_url' => $profile_photo_url,
                    //
                    'is_admin' => $is_admin,
                    'is_employee' => $is_employee,
                    'is_customer' => $is_customer,
                    //
                    'application_count' => $application_count,
                ];
            },
        ]);
    }
}
