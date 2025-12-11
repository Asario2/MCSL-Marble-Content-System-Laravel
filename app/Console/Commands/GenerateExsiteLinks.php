<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateExsiteLinks extends Command
{
    protected $signature = 'exsite:generate';
    protected $description = 'Generate HTML file from sitemap.SD.xml for asario.de/blogs links';

    public function handle()
    {
        $dom = "ab";
        $source = public_path('sitemap.xml');
        $target  = resource_path('exsite/exsite.'.$dom.'.htm');

        if (!file_exists($source)) {
            $this->error("Sitemap nicht gefunden: $source");
            return 1;
        }

        // XML einlesen
        $xml = simplexml_load_file($source);

        // Sicherheitscheck
        if (!$xml || !isset($xml->url)) {
            $this->error("Ungültige XML-Struktur.");
            return 1;
        }

        // HTML-Header erzeugen
        $html  = "<!DOCTYPE html>\n<html lang=\"de\">\n<head>\n<meta charset=\"UTF-8\">\n";
        $html .= "<title>ExSite Links</title>\n</head>\n<body>\n<ul>\n";

        // Alle <loc> durchgehen
        foreach ($xml->url as $entry) {
            if (isset($entry->loc)) {
                $url = trim((string)$entry->loc);
                $html .= "<li><a href=\"{$url}\">{$url}</a></li>\n";
            }
        }

        // Datei abschließen
        $html .= "</ul>\n</body>\n</html>";

        // Datei speichern
        file_put_contents($target, $html);

        $this->info("HTML-Datei erfolgreich erzeugt: $target");
        return 0;
    }
}
