<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DetectWatermarkPython extends Command
{
    protected $signature = 'watermark:detect-python
        {images : Ordner mit Bildern}
        {watermarks : Ordner mit Watermarks}';

    protected $description = 'Detect watermarks using Python + OpenCV';

    public function handle(): int
    {
        $images     = realpath($this->argument('images'));
        $watermarks = realpath($this->argument('watermarks'));

        if (! $images || ! $watermarks) {
            $this->error('Ungültige Pfade');
            return Command::FAILURE;
        }

        $script = base_path('_work/detect_watermark.py');

        $cmd = sprintf(
            'py %s %s %s',
            escapeshellarg($script),
            escapeshellarg($images),
            escapeshellarg($watermarks)
        );

        $output = shell_exec($cmd);
        $data   = json_decode($output, true);

        if (! $data) {
            $this->error('Python-Fehler oder kein JSON');
            return Command::FAILURE;
        }

        $this->info('✅ Mit Watermark:');
        foreach ($data['with_watermark'] as $img) {
            $this->line('  ✔ ' . $img);
        }

        $this->warn('❌ Ohne Watermark:');
        foreach ($data['without_watermark'] as $img) {
            $this->line('  ✖ ' . $img);
        }

        return Command::SUCCESS;
    }
}
