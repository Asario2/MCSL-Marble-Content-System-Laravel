<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanDuplicateCss extends Command
{
    protected $signature = 'css:clean-duplicates';
    protected $description = 'Finds duplicate CSS blocks in public/css/tailw/, compares with extra.css, moves duplicates to extra.css and removes them from originals';

    public function handle()
    {
        $cssDir = public_path('css/tailw');
        $extraFile = $cssDir . '/extra.css';

        if (!File::exists($cssDir)) {
            $this->error("Directory $cssDir not found!");
            return Command::FAILURE;
        }

        $seenBlocks = [];

        // Falls extra.css existiert, deren Blöcke als "bereits gesehen" merken
        if (File::exists($extraFile)) {
            $extraContent = File::get($extraFile);
            preg_match_all('/[^{]+\{[^}]+\}/s', $extraContent, $extraMatches);
            foreach ($extraMatches[0] as $block) {
                $seenBlocks[trim($block)] = true;
            }
        }

        $duplicates = [];
        $files = File::files($cssDir);

        foreach ($files as $file) {
            $filename = $file->getFilename();

            $this->info("Processing: " . $filename);
            $content = File::get($file->getPathname());

            // Blöcke mit Regex erfassen
            preg_match_all('/[^{]+\{[^}]+\}/s', $content, $matches);

            $uniqueBlocks = [];
            foreach ($matches[0] as $block) {
                $trimmedBlock = trim($block);

                if (isset($seenBlocks[$trimmedBlock])) {
                    // schon gesehen (in extra.css oder anderer Datei) → als Duplikat markieren
                    if ($filename !== 'extra.css') {
                        $duplicates[] = $trimmedBlock;
                    }
                } else {
                    $seenBlocks[$trimmedBlock] = true;
                    $uniqueBlocks[] = $trimmedBlock;
                }
            }

            // Nur Dateien außer extra.css überschreiben
            if ($filename !== 'extra.css') {
                File::put($file->getPathname(), implode(PHP_EOL . PHP_EOL, $uniqueBlocks));
            }
        }

        // Duplikate in extra.css anhängen (falls neu)
        if (!empty($duplicates)) {
            $uniqueDupes = array_unique($duplicates);
            File::append($extraFile, PHP_EOL . implode(PHP_EOL . PHP_EOL, $uniqueDupes) . PHP_EOL);
            $this->info(count($uniqueDupes) . " duplicate blocks moved to extra.css");
        } else {
            $this->info("No duplicate blocks found.");
        }

        return Command::SUCCESS;
    }
}
