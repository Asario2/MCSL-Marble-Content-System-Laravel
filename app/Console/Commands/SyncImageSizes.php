<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncImageSizes extends Command
{
    protected $signature = 'images:sync-sizes';
    protected $description = 'Sync image sizes from WITH to WITHOUT, delete both if aspect ratio differs';

    public function handle()
    {
        $withPath    = public_path('/images/copyleft/dataset/with');
        $withoutPath = public_path('/images/copyleft/dataset/without');

        if (!File::exists($withPath) || !File::exists($withoutPath)) {
            $this->error('❌ One or both directories do not exist');
            return Command::FAILURE;
        }

        $withImages = File::files($withPath);
        $this->info('🔍 Found ' . count($withImages) . ' images in WITH');

        foreach ($withImages as $withFile) {

            $filename = $withFile->getFilename();
            $withFilePath = $withFile->getPathname();
            $withoutFilePath = $withoutPath . DIRECTORY_SEPARATOR . $filename;

            // ❗ Wenn in WITHOUT fehlt → BOTH löschen
            if (!File::exists($withoutFilePath)) {
                File::delete($withFilePath);
                $this->warn("⚠️ Missing WITHOUT image: {$filename} → deleted BOTH");
                continue;
            }

            try {
                $withImg = new \Imagick($withFilePath);
                $withoutImg = new \Imagick($withoutFilePath);

                $withW  = $withImg->getImageWidth();
                $withH  = $withImg->getImageHeight();

                $withoutW = $withoutImg->getImageWidth();
                $withoutH = $withoutImg->getImageHeight();

                // Seitenverhältnis
                $withRatio    = round($withW / $withH, 4);
                $withoutRatio = round($withoutW / $withoutH, 4);

                // ❌ Unterschiedliches Seitenverhältnis → beide löschen
                if ($withRatio !== $withoutRatio) {
                    File::delete($withFilePath);
                    File::delete($withoutFilePath);

                    $this->error(
                        "🗑️ ASPECT RATIO MISMATCH ({$withW}x{$withH} vs {$withoutW}x{$withoutH}) → deleted BOTH {$filename}"
                    );
                    continue;
                }

                // ✅ Resize WITHOUT auf WITH-Größe
                $withoutImg->resizeImage($withW, $withH, \Imagick::FILTER_LANCZOS, 1);
                $withoutImg->writeImage($withoutFilePath);

                $this->info("✅ Synced {$filename} ({$withW}x{$withH})");

            } catch (\Throwable $e) {
                // Wenn irgendwas kaputt ist → beide löschen
                File::delete($withFilePath);
                File::delete($withoutFilePath);

                $this->error("❌ ERROR {$filename}: " . $e->getMessage() . " → deleted BOTH");
            }
        }

        $this->info('🏁 DONE');
        return Command::SUCCESS;
    }
}
