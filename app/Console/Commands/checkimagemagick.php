<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class checkimagemagick extends Command
{
    protected $signature = 'images:check-support';
    protected $description = 'Check available image processing support';

    public function handle()
    {
        $this->info('🔍 Prüfe verfügbare Bildverarbeitungs-Unterstützung...');

        // Check GD
        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
            $this->info('✅ GD ist verfügbar: ' . ($gdInfo['GD Version'] ?? 'Unknown'));
        } else {
            $this->error('❌ GD ist nicht verfügbar');
        }

        // Check ImageMagick
        if (extension_loaded('imagick')) {
            $this->info('✅ ImageMagick ist verfügbar');
        } else {
            $this->warn('⚠️ ImageMagick ist nicht verfügbar');
        }

        // Check EXIF
        if (function_exists('exif_imagetype')) {
            $this->info('✅ EXIF ist verfügbar');
        } else {
            $this->warn('⚠️ EXIF ist nicht verfügbar');
        }

        $this->info('');
        $this->info('💡 Empfehlung: Installieren Sie GD oder ImageMagick für bessere Bildverarbeitung');
    }
}
