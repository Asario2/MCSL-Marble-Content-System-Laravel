<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveDuplicatedExtra extends Command
{
    protected $signature = 'css:remove-duplicates';
    protected $description = 'Entfernt doppelte CSS-Zeilen in public/css/tailw/extra.css';

    public function handle()
    {
        $filePath = public_path('css/tailw/extra.css');

        if (!File::exists($filePath)) {
            $this->error("Datei extra.css nicht gefunden in public/css/tailw/");
            return Command::FAILURE;
        }

        $content = File::get($filePath);

        // Zeilen in Array splitten
        $lines = preg_split('/\r\n|\r|\n/', $content);

        // Duplikate entfernen
        $uniqueLines = array_unique(array_map('trim', $lines));

        // Leerzeilen entfernen
        $uniqueLines = array_filter($uniqueLines, fn($line) => $line !== '');

        // Wieder in Datei schreiben
        File::put($filePath, implode(PHP_EOL, $uniqueLines) . PHP_EOL);

        $this->info("Duplikate aus extra.css erfolgreich entfernt. (" . count($lines) . " -> " . count($uniqueLines) . " Zeilen)");

        return Command::SUCCESS;
    }
}
