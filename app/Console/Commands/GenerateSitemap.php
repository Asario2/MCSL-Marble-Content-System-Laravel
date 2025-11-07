<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {SD}';
    protected $description = 'Generate sitemap.xml from all public GET routes and dynamic picture subpages (filtered by subdomain middleware)';

    public function handle()
    {
        $this->info('Sitemap wird erstellt …');

        $currentSD = $this->argument('SD');

        // === 1️⃣ Statische Laravel-Routen erfassen ===
        $routes = collect(Route::getRoutes())
            ->filter(function ($route) use ($currentSD) {
                if (!in_array('GET', $route->methods()) || str_contains($route->uri(), '{')) {
                    return false;
                }

                if (str_starts_with($route->uri(), 'api/')) {
                    return false;
                }

                $middlewares = $route->gatherMiddleware();
                $excluded = ['is_admin', 'auth', 'verified'];
                foreach ($excluded as $mw) {
                    if (in_array($mw, $middlewares)) {
                        return false;
                    }
                }

                foreach ($middlewares as $mw) {
                    if (is_string($mw) && str_starts_with($mw, \App\Http\Middleware\CheckSubd::class)) {
                        $parts = explode(':', $mw, 2);
                        if (count($parts) === 2) {
                            $allowed = explode(',', $parts[1]);
                            if (in_array($currentSD, $allowed)) {
                                return true;
                            }
                        }
                        return false;
                    }
                }

                return false;
            })
            ->map(fn($route) => url($route->uri()))
            ->unique()
            ->values();

        // === 2️⃣ Dynamische Picture-Seiten ergänzen ===
        $pictures = DB::table('image_categories')
            ->select('name as slug', 'updated_at')
            ->get(); // Erst get() liefert Collection

        $pictureLinks = $pictures->map(function ($p) use ($currentSD) {
            return [
                'loc' => $this->EXTR_LNK(url('/home/show/pictures/' . $p->slug), $currentSD),
                'lastmod' => $p->updated_at ?? now(),
            ];
        });

        // === 3️⃣ XML zusammenbauen ===
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        // Statische Routen hinzufügen
        foreach ($routes as $link) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($this->EXTR_LNK($link, $currentSD), ENT_XML1));
            $url->addChild('lastmod', now()->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.5');
        }

        // Dynamische Picture-Seiten hinzufügen
        foreach ($pictureLinks as $entry) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild('lastmod', Carbon::parse($entry['lastmod'])->toAtomString());
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', '0.7');
        }

        // === 4️⃣ Datei speichern ===
        $path = public_path("sitemap." . $currentSD . ".xml");
        $xml->asXML($path);

        $this->info("✅ Sitemap für Subdomain {$currentSD} erstellt: {$path}");
    }

    function EXTR_LNK($url, $sd)
    {
        return str_replace("http://localhost/", "https://" . Settings::$dom[$sd] . "/", $url);
    }
}
