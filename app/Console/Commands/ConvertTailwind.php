<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertTailwind extends Command
{
    protected $signature = 'app:convert-tailwind {input} {output}';
    protected $description = 'Übernimmt vorhandene Farbwerte (inkl. sun/night) aus einer JSON in eine Tailwind-Konfiguration.';

    public function handle()
    {
        $inputFile = $this->argument('input');
        $outputFile = $this->argument('output');

        if (!File::exists($inputFile)) {
            $this->error("Datei nicht gefunden: {$inputFile}");
            return 1;
        }

        $content = File::get($inputFile);
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Ungültiges JSON in {$inputFile}: " . json_last_error_msg());
            return 1;
        }

        if (!isset($json['theme']['extend']['colors'])) {
            $this->error("Keine gültige Farbstruktur in {$inputFile} gefunden.");
            return 1;
        }

        $colors = $json['theme']['extend']['colors'];

        // === Tailwind Config Grundgerüst ===
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

        // === Farben ausgeben ===
        foreach ($colors as $group => $data) {
            $output .= "\n                \"{$group}\": {";
            foreach ($data as $subgroup => $values) {
                // Prüfen, ob das ein Array von Shades ist (sun/night)
                if (is_array($values) && isset($values['50'])) {
                    // einfache Struktur, z. B. "layout": { "50": "#fff", ... }
                    foreach ($values as $shade => $hex) {
                        $output .= "\n                    \"{$shade}\": \"{$hex}\",";
                    }
                } elseif (is_array($values)) {
                    // verschachtelte Struktur (sun/night)
                    foreach ($values as $mode => $shades) {
                        $output .= "\n                    \"{$mode}\": {";
                        foreach ($shades as $shade => $hex) {
                            $output .= "\n                        \"{$shade}\": \"{$hex}\",";
                        }
                        $output = rtrim($output, ','); // letztes Komma entfernen
                        $output .= "\n                    },";
                    }
                }
            }
            $output = rtrim($output, ',');
            $output .= "\n                },";
        }

        // === Restlicher Output ===
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
        $this->info("Tailwind-Konfiguration erfolgreich erstellt → {$outputFile}");

        return 0;
    }
}
