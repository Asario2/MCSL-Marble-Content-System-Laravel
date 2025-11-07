<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertTailwind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-tailwind {input} {output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Konvertiert eine einfache Tailwind color config in eine Dark/Light-Color-Konfiguration';

    /**
     * Execute the console command.
     */
     public function handle()
    {
        $inputFile = $this->argument('input');
        $outputFile = $this->argument('output');

        if (!File::exists($inputFile)) {
            $this->error("Datei nicht gefunden: {$inputFile}");
            return 1;
        }

        $content = File::get($inputFile);

        // Entfernt module.exports, Kommentare, usw.
        $content = preg_replace('/module\.exports\s*=\s*/', '', $content);
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        $content = preg_replace('/\/\/.*$/m', '', $content);

        // JS -> JSON kompatibel machen
        $content = str_replace("'", '"', $content);
        $content = preg_replace('/,\s*([}\]])/', '$1', $content); // letzte Kommata entfernen

        // Versuch, JS-Objekt in PHP-Array zu decodieren
        $json = json_decode($content, true);

        if (!isset($json['theme']['extend']['colors'])) {
            $this->error("Keine gültige Farbstruktur in {$inputFile} gefunden.");
            return 1;
        }

        $baseColors = $json['theme']['extend']['colors'];
        $sunNightColors = [];

        foreach ($baseColors as $name => $variants) {
            foreach ($variants as $key => $value) {
                // Key als String sichern
                $sunNightColors["{$name}-sun-{$key}"] = $value;
            }

            $reversed = array_reverse($variants, true);
            foreach ($reversed as $key => $value) {
                $sunNightColors["{$name}-night-{$key}"] = $value;
            }
        }

        // ESM Output
        $output = <<<EOT
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            colors: {
EOT;

       foreach ($sunNightColors as $key => $value) {
    // numerische Keys oder gemischte Keys als Strings schreiben
    $output .= "\n                \"{$key}\": \"{$value}\",";
}

        $output .= <<<EOT

            },
            fontFamily: {
                sans: ["Open Sans", ...defaultTheme.fontFamily.sans],
                logo: ["Open Sans", ...defaultTheme.fontFamily.sans],
                title: ["Ubuntu", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
EOT;

        File::put($outputFile, $output);
        $this->info("Tailwind-Konfiguration erfolgreich konvertiert → {$outputFile}");

        return 0;
    }
}


