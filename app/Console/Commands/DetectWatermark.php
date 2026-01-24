<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Imagick;

class DetectWatermark extends Command
{
    protected $signature = 'watermark:detect
                            {path : Ordner mit JPGs}';

    protected $description = 'Detect images without any known watermark (multi watermark, stretched safe)';

    public function handle(): int
    {
        
    }
    // public function handle(): int
    // {
    //     $imagePath = rtrim($this->argument('path'), '/');
    //     $wmDir     = public_path('/images/copyleft/');

    //     if (! is_dir($wmDir)) {
    //         $this->error('Watermark-Ordner nicht gefunden: ' . $wmDir);
    //         return Command::FAILURE;
    //     }

    //     $this->info('🔍 Lade Watermarks …');

    //     // 🔹 Alle Watermarks laden + vorbereiten
    //     $allVariants = [];

    //     foreach (glob($wmDir . '/*.{png,jpg,jpeg}', GLOB_BRACE) as $wmFile) {
    //         $wmBase     = $this->preprocess($wmFile);
    //         $variants   = $this->generateVariants($wmBase);
    //         $allVariants = array_merge($allVariants, $variants);
    //     }

    //     if (! count($allVariants)) {
    //         $this->error('Keine Watermarks gefunden');
    //         return Command::FAILURE;
    //     }

    //     $this->info('✔ ' . count($allVariants) . ' Watermark-Varianten generiert');

    //     $withoutWatermark = [];

    //     foreach (glob($imagePath . '/*.jpg') as $file) {

    //         try {
    //             $image = $this->preprocess($file);

    //             if (! $this->containsWatermark($image, $allVariants)) {
    //                 $withoutWatermark[] = basename($file);
    //             }

    //             $image->clear();
    //             $image->destroy();

    //         } catch (\Throwable $e) {
    //             $this->warn('⚠ Fehler bei ' . basename($file));
    //         }
    //     }

    //     // Ergebnis
    //     if (count($withoutWatermark)) {
    //         $this->warn('❌ Bilder OHNE Watermark:');
    //         foreach ($withoutWatermark as $img) {
    //             $this->line(' - ' . $img);
    //         }
    //     } else {
    //         $this->info('✅ Alle Bilder enthalten ein bekanntes Watermark');
    //     }

    //     return Command::SUCCESS;
    // }

    // /**
    //  * Vereinheitlicht Bilder (Graustufen, Kanten, Threshold)
    //  */
    // private function preprocess(string $path): Imagick
    // {
    //     $img = new Imagick($path);

    //     // Alpha behalten!
    //     $img->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);

    //     // Für Vergleich vereinheitlichen
    //     $img->setImageColorspace(Imagick::COLORSPACE_GRAY);
    //     $img->contrastImage(1);

    //     return $img;
    // }
    // private function extractBottomRightArea(Imagick $image, int $wmWidth, int $wmHeight): Imagick
    // {
    //     $crop = clone $image;

    //     $x = $image->getImageWidth()  - $wmWidth  - 25;
    //     $y = $image->getImageHeight() - $wmHeight - 25;

    //     $crop->cropImage(
    //         $wmWidth,
    //         $wmHeight,
    //         max(0, $x),
    //         max(0, $y)
    //     );

    //     return $crop;
    // }

    // /**
    //  * Erzeugt gestauchte & skalierte Watermark-Varianten
    //  */
    // private function generateVariants(Imagick $wm): array
    // {
    //     $variants = [];
    //     $scales   = [0.7, 0.85, 1.0, 1.15, 1.3];

    //     foreach ($scales as $x) {
    //         foreach ($scales as $y) {

    //             $v = clone $wm;

    //             $v->resizeImage(
    //                 max(1, (int) ($wm->getImageWidth()  * $x)),
    //                 max(1, (int) ($wm->getImageHeight() * $y)),
    //                 Imagick::FILTER_LANCZOS,
    //                 1
    //             );

    //             $variants[] = $v;
    //         }
    //     }

    //     return $variants;
    // }

    // /**
    //  * Prüft, ob irgendein Watermark enthalten ist
    //  */
    // private function containsWatermark(Imagick $image, array $variants): bool
    // {
    //     foreach ($variants as $wm) {

    //         $wmW = $wm->getImageWidth();
    //         $wmH = $wm->getImageHeight();

    //         if ($wmW > $image->getImageWidth() || $wmH > $image->getImageHeight()) {
    //             continue;
    //         }

    //         // 🔹 Nur rechte untere Ecke prüfen
    //         $region = $this->extractBottomRightArea($image, $wmW, $wmH);

    //         // 🔹 Watermark auf Region legen
    //         $compare = clone $region;
    //         $compare->compositeImage(
    //             $wm,
    //             Imagick::COMPOSITE_OVER,
    //             0,
    //             0
    //         );

    //         // 🔍 Differenz messen
    //         [$diff, $error] = $region->compareImages(
    //             $compare,
    //             Imagick::METRIC_MEANSQUAREERROR
    //         );

    //         $region->clear();
    //         $compare->clear();
    //         $diff->clear();

    //         // 🔧 sehr zuverlässig bei transparentem WM
    //         if ($error < 0.02) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }
}
