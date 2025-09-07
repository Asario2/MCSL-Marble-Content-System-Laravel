<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a sitemap.xml from all GET routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sitemap wird erstellt …');

        $routes = collect(Route::getRoutes())
            ->filter(function ($route) {
                // Nur GET-Routen ohne Parameter
                return in_array('GET', $route->methods()) && !str_contains($route->uri(), '{');
            })
            ->map(function ($route) {
                return url($route->uri());
            })
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
            $url->addChild('loc', htmlspecialchars($link, ENT_XML1));
            $url->addChild('lastmod', now()->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.5');
        }

        $path = public_path('sitemap.xml');
        $xml->asXML($path);

        $this->info("✅ Sitemap erfolgreich erstellt: {$path}");
    }
}
