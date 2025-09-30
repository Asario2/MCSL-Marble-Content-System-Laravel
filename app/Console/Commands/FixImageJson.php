<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Imagick;

class FixImageJson extends Command
{
    protected $signature = 'images:fix-json';
    protected $description = 'Fix image JSON filenames and update width/height using ImageMagick';

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
            $files = glob($dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
            $fileMap = [];
            foreach ($files as $file) {
                $fileMap[strtolower(basename($file))] = basename($file);
            }

            $hasChanges = false;

            foreach ($json as &$entry) {
                if (!isset($entry['filename'])) continue;

                $originalFilename = $entry['filename'];
                $lower = strtolower($originalFilename);

                // Korrigiere Dateinamen falls nötig
                if (isset($fileMap[$lower]) && $originalFilename !== $fileMap[$lower]) {
                    $this->line("Korrigiere Dateiname {$originalFilename} → {$fileMap[$lower]}");
                    $entry['filename'] = $fileMap[$lower];
                    $hasChanges = true;
                }

                // Versuche verschiedene Pfade für die Bilddatei
                $imagePath = $this->findImageFile($dir, $entry['filename']);

                if (!$imagePath) {
                    $this->warn("Datei nicht gefunden: {$entry['filename']}");
                    continue;
                }

                // Verwende ImageMagick für Dimensionserkennung
                $dimensions = $this->getImageDimensions($imagePath);

                if ($dimensions) {
                    $entry['width'] = $dimensions['width'];
                    $entry['height'] = $dimensions['height'];
                    $this->line("Setze Dimensionen für {$entry['filename']}: {$dimensions['width']}x{$dimensions['height']}");
                    $hasChanges = true;
                } else {
                    $this->warn("Kann Größe von {$imagePath} nicht ermitteln");
                }

                // Optional: Bildformat hinzufügen
                if (!isset($entry['format'])) {
                    $format = $this->getImageFormat($imagePath);
                    if ($format) {
                        $entry['format'] = $format;
                        $this->line("Setze Format für {$entry['filename']}: {$format}");
                        $hasChanges = true;
                    }
                }

                // Optional: Dateigröße hinzufügen
                if (!isset($entry['filesize'])) {
                    $filesize = filesize($imagePath);
                    if ($filesize) {
                        $entry['filesize'] = $filesize;
                        $this->line("Setze Dateigröße für {$entry['filename']}: " . $this->formatBytes($filesize));
                        $hasChanges = true;
                    }
                }
            }

            if ($hasChanges) {
                file_put_contents($jsonFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                $this->info("✅ {$jsonFile} aktualisiert");
            } else {
                $this->info("ℹ️  Keine Änderungen für {$jsonFile}");
            }
        }

        $this->info('🎉 Alle JSON-Dateien wurden verarbeitet.');
    }

    /**
     * Findet die Bilddatei in verschiedenen Unterverzeichnissen
     */
    private function findImageFile(string $dir, string $filename): ?string
    {
        $possiblePaths = [
            $dir . '/big/' . $filename,
            $dir . '/thumbs/' . $filename,
            $dir . '/' . $filename,
            $dir . '/original/' . $filename,
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->line("📁 Gefunden: {$path}");
                return $path;
            }
        }

        return null;
    }

    /**
     * Ermittelt Bildabmessungen mit ImageMagick
     */
    private function getImageDimensions(string $imagePath): ?array
{
    try {
        $imagick = new \Imagick($imagePath);

        // ganz wichtig: Orientierung aus EXIF anwenden
        $imagick->setImageOrientation(\Imagick::ORIENTATION_UNDEFINED);
        $imagick->autoOrient();

        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        $imagick->clear();
        $imagick->destroy();

        return [
            'width' => $width,
            'height' => $height
        ];
    } catch (\Exception $e) {
        $this->error("ImageMagick Fehler für {$imagePath}: " . $e->getMessage());
        return $this->getImageDimensionsFallback($imagePath);
    }
}
    /**
     * Fallback für Dimensionserkennung ohne ImageMagick
     */
    private function getImageDimensionsFallback(string $imagePath): ?array
    {
        $size = @getimagesize($imagePath);
        if ($size && isset($size[0], $size[1])) {
            $this->line("⚠️  Verwende Fallback für Dimensionserkennung: {$imagePath}");
            return [
                'width' => $size[0],
                'height' => $size[1]
            ];
        }

        return null;
    }

    /**
     * Ermittelt das Bildformat
     */
    private function getImageFormat(string $imagePath): ?string
    {
        try {
            $imagick = new Imagick();
            $imagick->readImage($imagePath);
            $format = $imagick->getImageFormat();
            $imagick->clear();
            $imagick->destroy();

            return $format;
        } catch (\Exception $e) {
            // Fallback: verwende Dateiendung
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            return $extension ?: null;
        }
    }

    /**
     * Formatiert Bytes in lesbare Größe
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Prüft ob ImageMagick verfügbar ist
     */
    private function isImageMagickAvailable(): bool
    {
        try {
            new Imagick();
            return true;
        } catch (\Exception $e) {
            $this->warn('ImageMagick ist nicht verfügbar. Verwende Fallback-Methoden.');
            return false;
        }
    }
}
