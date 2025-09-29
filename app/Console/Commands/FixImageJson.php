<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixImageJson extends Command
{
    protected $signature = 'images:fix-json';
    protected $description = 'Fix image JSON filenames and update width/height using getimagesize';

    public function handle()
    {
        $basePath = public_path('images/_mfx/images/imgdir_content');
        $dirs = glob($basePath . '/*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $jsonFile = $dir . '/index.json';
            if (!file_exists($jsonFile)) {
                $this->warn("Keine index.json in {$dir}");
                continue;
            }

            $this->info("Verarbeite {$jsonFile}");
            $json = json_decode(file_get_contents($jsonFile), true);

            if (!is_array($json)) {
                $this->error("Ungültige JSON in {$jsonFile}");
                continue;
            }

            // Dateien im aktuellen Verzeichnis einlesen
            $files = glob($dir . '/*.jpg');
            $fileMap = [];
            foreach ($files as $file) {
                $fileMap[strtolower(basename($file))] = basename($file);
            }

            foreach ($json as &$entry) {
                if (!isset($entry['filename'])) continue;

                $lower = strtolower($entry['filename']);
                if (isset($fileMap[$lower]) && $entry['filename'] !== $fileMap[$lower]) {
                    $this->line("Korrigiere Dateiname {$entry['filename']} → {$fileMap[$lower]}");
                    $entry['filename'] = $fileMap[$lower];
                }

                // 1. Versuche /big/<filename>
                $imagePath = $dir . '/big/' . $entry['filename'];
                if (!file_exists($imagePath)) {
                    // 2. Fallback: derselbe Ordner wie index.json
                    $imagePath = $dir . '/' . $entry['filename'];
                    if (!file_exists($imagePath)) {
                        $this->warn("Datei nicht gefunden: {$entry['filename']}");
                        continue;
                    } else {
                        $this->line("Verwende Fallback-Datei: {$imagePath}");
                    }
                }

                $size = getimagesize($imagePath);
                if ($size) {
                    $entry['width'] = $size[0];
                    $entry['height'] = $size[1];
                    $this->line("Setze Dimensionen für {$entry['filename']}: {$size[0]}x{$size[1]}");
                } else {
                    $this->warn("Kann Größe von {$imagePath} nicht ermitteln");
                }
            }

            file_put_contents($jsonFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $this->info('Alle JSON-Dateien wurden aktualisiert.');
    }
}
