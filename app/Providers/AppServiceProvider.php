<?php

namespace App\Providers;
use App\Http\Controllers\GlobalController;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App\Models\Settings;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        // Globaler Domain-Switch (z. B. für Multi-Tenant)
        if (app()->runningInConsole()) {
            return; // Verhindere Ausführung bei Artisan-Commands
        }


        require_once app_path('helpers.php');


        // app()->singleton(LaravelCookieConsent::class, function ($app) {
        //     $consent = new LaravelCookieConsent();

        //     $consent->setCookieDomain(request()->getHost()); // <- Dynamisch

        //     return $consent;
        // });
        // Config::set('cookieconsent.cookie.domain', request()->getHost());
        // app(CookieConsent::class)->useCookieName('cookie_consent_' . str_replace('.', '_', request()->getHost()));
        // app(LaravelCookieConsent::class)->useCookieName(
        //     'cookie_consent_' . str_replace('.', '_', request()->getHost())
        // );

        // $host = str_replace('.', '_', request()->getHost());

        // app(CookieConsentManager::class)->useCookieName('cookie_consent_' . $host);

        // $host = request()->getHost(); // z. B. blog.domain.de
        // $slug = str_replace(['.', ':'], '_', $host); // daraus wird z. B. "blog_domain_de"

        // CookiesManager::useCookieName('cookie_consent_' . $slug);

        // $host = str_replace('.', '_', request()->getHost());

     //  CookieConsent::useCookieName('cookie_consent_' . $host);

        $subb = explode('.', str_replace("www.",'',request()->getHost()))[0];
        switch($subb){
            case "asario":
                $subb = "ab";
            break;
            case "monikadargies":
                $subb = "dag";
            break;
            case "marblefx":
                $subb = "mfx";
            break;
            case "mjs":
                $subb = "mjs";
            break;
            case "ra-c-henning":
                $subb = "chh";
            break;
            case "217":
            $subb = "mfx";
            break;
            default:
            $subb = $subb;
            break;
        }
        $subb2 = $subb;
        $url = $_SERVER['REQUEST_URI'];
        if(substr_count(@$url,"/login")
            || substr_count($url,"/admin/")
            || substr_count($url,"reset-password")
            || substr_count($url,"register")
            || substr_count($url,"reset-password")
            || substr_count($url,"forgot-password")
            || substr_count($url,"email/verify")
            || substr_count($url,"confirm-password")
            || substr_count($url,"verify-email"))

        {
            $subb2  = "ab";
            // dd(rawurldecode("%7B%22consent_at%22%3A1752311194%2C%22laravel_cookie_consent%22%3Atrue%2C%22laravel_session%22%3Atrue%2C%22XSRF-TOKEN%22%3Atrue%2C%22darkmode_enabled%22%3Afalse%7D"));
        }

        View::share('subdomain', $subb);
        View::share('sd_alt', $subb2);
        GlobalController::SetDomain();
        $domSettings = Settings::$dom; // Array
        config(['app.name_alt' => $domSettings[SD()] ?? 'default.domain.com']);
    }
}
