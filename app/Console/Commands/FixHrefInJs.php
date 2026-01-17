<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixHrefInJs extends Command
{
    protected $signature = 'fix:href-js';
    protected $description = 'Fix JS files with hardcoded absolute paths';

    public function handle()
    {
        // ⚠️ HIER absolute Pfade eintragen (Windows oder Linux)
        $files = [
            base_path('node_modules/@inertiajs/core/dist/index.esm.js'),
            base_path('node_modules/@inertiajs/core/dist/index.js'),
            base_path('node_modules\.vite\deps\@inertiajs_vue3.js'),
        ];

        foreach ($files as $filePath) {

            if (!File::exists($filePath)) {
                $this->warn("File does not exist: $filePath");
                continue;
            }

            $content = File::get($filePath);

            // Wenn der Block schon existiert -> skip
            if (strpos($content, "if(!href){\n  href = '';\n}") !== false) {
                $this->info("Already fixed: $filePath");
                continue;
            }

            // Wenn die Zielzeile existiert -> ersetzen
            if (strpos($content, "const hasHost = urlHasProtocol(href.toString());") !== false) {

                $content = str_replace(
                    "const hasHost = urlHasProtocol(href.toString());",
                    "if(!href){\n  href = '';\n}\nconst hasHost = urlHasProtocol(href.toString());",
                    $content
                );

                File::put($filePath, $content);

                $this->info("Fixed: $filePath");
            } else {
                $this->warn("No match in: $filePath");
            }
        }

        $this->info('Done.');
    }
}
