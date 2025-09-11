<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use App\Models\Settings;
class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {SD}';
    protected $description = 'Generate a sitemap.xml from all public GET routes (filtered by subdomain middleware)';

    public function handle()
    {
        $this->info('Sitemap wird erstellt …');

        $currentSD = $this->argument('SD');


        $routes = collect(Route::getRoutes())
            ->filter(function ($route) use ($currentSD) {
                // Nur GET-Routen ohne Parameter
                if (!in_array('GET', $route->methods()) || str_contains($route->uri(), '{')) {
                    return false;
                }

                // API-Routen ausschließen
                if (str_starts_with($route->uri(), 'api/')) {
                    return false;
                }

                $middlewares = $route->gatherMiddleware();

                // Standardmäßig geschützte Routen ausschließen
                $excluded = ['is_admin', 'auth', 'verified'];
                foreach ($excluded as $mw) {
                    if (in_array($mw, $middlewares)) {
                        return false;
                    }
                }

                // Prüfen, ob Route die Subdomain-Middleware hat
                foreach ($middlewares as $mw) {
                    if (is_string($mw) && str_starts_with($mw, \App\Http\Middleware\CheckSubd::class)) {
                        // Parameter extrahieren (z.B. "ab,asario")
                        $parts = explode(':', $mw, 2);
                        if (count($parts) === 2) {
                            $allowed = explode(',', $parts[1]);
                            if (in_array($currentSD, $allowed)) {
                                return true; // passt zu SD()
                            }
                        }
                        return false; // hat Middleware, aber SD() passt nicht
                    }
                }

                // Falls gar keine CheckSubd-Middleware → ignorieren
                return false;
            })
            ->map(fn($route) => url($route->uri()))
            ->unique()
            ->values();

        if ($routes->isEmpty()) {
            $this->warn('Keine passenden Routen gefunden.');
            return;
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($routes as $link) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($this->EXTR_LNK($link,$currentSD), ENT_XML1));
            $url->addChild('lastmod', now()->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.5');
        }

        $path = public_path("sitemap." . $currentSD . ".xml");
        $xml->asXML($path);

        $this->info("✅ Sitemap für Subdomain {$currentSD} erstellt: {$path}");
    }
    function EXTR_LNK($url,$sd)
    {
        return str_replace("http://localhost/","https://".Settings::$dom[$sd]."/",$url);
    }
}
