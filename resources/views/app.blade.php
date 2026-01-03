    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('memory_limit', '-1');
    error_reporting(E_ALL);

    use Illuminate\Support\Facades\Auth;
    use App\Http\Controllers\GlobalController;
    App::setLocale('de');
    $subdomain = SD(); // z.B. "foo", "bar"
    $pagen = SD("pn");
    $favicon = "/images/_{$subdomain}/web/alogo.png";
    $ahost = request()->getHost();
    $glo = new GlobalController();

    if (!file_exists(public_path($favicon))) {
        $favicon = "/images/favicon_default.png";
    }
    if (isset($_GET['re']) && $_GET['re'] === '1') {
    header("Location: http://".$_SERVER['HTTP_HOST'].str_replace("re=1",'',$_SERVER['REQUEST_URI']));
    exit;
    }
    ?>

    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ CleanTable(1) }}</title>

        <script>
            if (typeof global === 'undefined') window.global = window;
            window.Laravel = { userId: {{ auth()->id() ?? 'null' }} };
            window.authid = "{{ Auth::id() }}";
            window.subdomain = "{{ $subdomain }}";
            window.subdomain_alt = "{{ $sd_alt ?? '' }}";
            window.pagename = "{{ $pagen }}";
            window.ahost = "{{ $ahost }}";
            window.app_name = "{{ $pagen }}";
        </script>

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ $favicon }}">

        <script src="/js/jquery-3.6.0.min.js"></script>
        <script src="/js/users.js"></script>

        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link href="/css/tailw/extra.css?time={{ time() }}" rel="stylesheet">
        <link href="/css/tailw/{{ $sd_alt ?? 'default' }}.css?time={{ time() }}" rel="stylesheet">

        <link rel="stylesheet" href="/Shariff/shariff.complete.css">
        <script src="/Shariff/shariff.min.js"></script>

        <img src="{{ route('countpixel', [
            'url' => urlencode(request()->fullUrl()),
            'route' => Route::currentRouteName() ?? 'unknown'
        ]) }}" alt="" width="1" height="1" style="display:none;">

        @inertiaHead

    {!! CookieConsent::styles() !!}
    <script>
    (function () {
        function handleReload() {
            const url = new URL(window.location.href);

            if (url.searchParams.get('re') === '1') {
                url.searchParams.delete('re');
                window.location.replace(url.toString());
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', handleReload);
        } else {
            // DOM ist bereits geladen → sofort ausführen
            handleReload();
        }
    })();
    </script>
    </head>
    <body class="font-sans antialiased">

        @php Notify(); @endphp
        <input type="hidden" id="token" value="{{ csrf_token() }}">
        @routes
        @inertia


        {!! CookieConsent::scripts(options: [
            'cookie_lifetime' => config('laravel-cookie-consent.cookie_lifetime', 7),
            'reject_lifetime' => config('laravel-cookie-consent.reject_lifetime', 1),
            'disable_page_interaction' => config('laravel-cookie-consent.disable_page_interaction', true),
            'preferences_modal_enabled' => config('laravel-cookie-consent.preferences_modal_enabled', true),
            'consent_modal_layout' => config('laravel-cookie-consent.consent_modal_layout', 'bar-inline'),
            'flip_button' => config('laravel-cookie-consent.flip_button', true),
            'theme' => config('laravel-cookie-consent.theme', 'default'),
            'cookie_prefix' => config('laravel-cookie-consent.cookie_prefix', 'Laravel_App'),
            'policy_links' => config('laravel-cookie-consent.policy_links', [
                ['text' => CookieConsent::translate('Privacy Policy'), 'link' => url('privacy-policy')],
                ['text' => CookieConsent::translate('Terms & Conditions'), 'link' => url('terms-and-conditions')],
            ]),
            'cookie_categories' => config('laravel-cookie-consent.cookie_categories', [
                'necessary' => [
                    'enabled' => true,
                    'locked' => true,
                    'js_action' => 'loadGoogleAnalytics',
                    'title' => CookieConsent::translate('Essential Cookies'),
                    'description' => CookieConsent::translate('These cookies are essential for the website to function properly.'),
                ],
                'analytics' => [
                    'enabled' => env('COOKIE_CONSENT_ANALYTICS', false),
                    'locked' => false,
                    'checked_by_default' => false,

                    'title' => CookieConsent::translate('Analytics Cookies'),
                    'description' => CookieConsent::translate('These cookies help us understand how visitors interact with our website.'),
                ],
                'marketing' => [
                    'enabled' => env('COOKIE_CONSENT_MARKETING', false),
                    'locked' => false,
                    'selected' => false,
                    'js_action' => 'loadFacebookPixel',
                    'title' => CookieConsent::translate('Marketing Cookies'),
                    'description' => CookieConsent::translate('These cookies are used for advertising and tracking purposes.'),
                ],
                'preferences' => [
                    'enabled' => env('COOKIE_CONSENT_PREFERENCES', false),
                    'locked' => false,
                    'js_action' => 'loadPreferencesFunc',
                    'title' => CookieConsent::translate('Preferences Cookies'),
                    'description' => CookieConsent::translate('These cookies allow the website to remember user preferences.'),
                ],
            ]),
            'cookie_title' => CookieConsent::translate('Cookie Disclaimer'),
            'cookie_description' => CookieConsent::translate('This website uses cookies to enhance your browsing experience, analyze site traffic, and personalize content. By continuing to use this site, you consent to our use of cookies.'),
            'cookie_modal_title' => CookieConsent::translate('Cookie Preferences'),
            'cookie_modal_intro' => CookieConsent::translate('You can customize your cookie preferences below.'),
            'cookie_accept_btn_text' => CookieConsent::translate('Accept All'),
            'cookie_reject_btn_text' => CookieConsent::translate('Reject All'),
            'cookie_preferences_btn_text' => CookieConsent::translate('Manage Preferences'),
            'cookie_preferences_save_text' => CookieConsent::translate('Save Preferences'),
        ]) !!}
        <script>
            @if(session('force_reload'))
                sessionStorage.setItem("force_reload", "true");
            @endif
        </script>

        <style>
            /* Fix für kurzes Aufblitzen des Banners */
            .cc-window { display: inline-block !important; }
        </style>
    </body>
    </html>
