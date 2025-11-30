<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixImageJsonFilenames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:fix-json-filenames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Korrigiert Dateinamen in index.json Dateien entsprechend der tatsächlichen Groß-/Kleinschreibung der Bilddateien.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = public_path('images/_mfx/images/imgdir_content');

        $this->info("Scanne Verzeichnis: $basePath");

        // Alle index.json Dateien finden
        $jsonFiles = File::allFiles($basePath);

        foreach ($jsonFiles as $file) {
            if ($file->getFilename() !== 'index.json') {
                continue;
            }

            $folderPath = $file->getPath();
            $jsonPath   = $file->getRealPath();

            $this->info("Bearbeite: $jsonPath");

            $jsonContent = json_decode(File::get($jsonPath), true);
            if (!is_array($jsonContent)) {
                $this->error("Fehler beim Parsen: $jsonPath");
                continue;
            }

            // Alle Dateien im Ordner
            $imageFiles = collect(File::files($folderPath))
                ->filter(fn($f) => strtolower($f->getExtension()) === 'jpg')
                ->mapWithKeys(fn($f) => [strtolower($f->getFilename()) => $f->getFilename()]);

            $changed = false;

            foreach ($jsonContent as &$entry) {
                if (!isset($entry['filename'])) {
                    continue;
                }

                $lower = strtolower($entry['filename']);
                if ($imageFiles->has($lower) && $entry['filename'] !== $imageFiles[$lower]) {
                    $this->line(" - Korrigiere {$entry['filename']} → {$imageFiles[$lower]}");
                    $entry['filename'] = $imageFiles[$lower];
                    $changed = true;
                }
            }

            if ($changed) {
                File::put($jsonPath, json_encode($jsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $this->info("✅ Änderungen gespeichert in $jsonPath");
            } else {
                $this->info("Keine Änderungen nötig in $jsonPath");
            }
        }

        $this->info("Fertig ✅");
        return Command::SUCCESS;
    }
}
