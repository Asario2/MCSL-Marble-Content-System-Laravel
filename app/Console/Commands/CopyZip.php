<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CopyZip extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zip:copy
        {--file=tmp/meine_bilder.zip : Pfad zur ZIP-Datei relativ zum public-Ordner}';

    /**
     * The console command description.
     */
    protected $description = 'Kopiert eine ZIP-Datei in einen festen Zielordner';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $zipUrl     = $this->option("file");
        $zielOrdner = 'E:/backup/images_dump';

        if (!is_dir($zielOrdner)) {
            mkdir($zielOrdner, 0755, true);
        }

        $zielPfad = rtrim($zielOrdner, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . basename(parse_url($zipUrl, PHP_URL_PATH));

        $zipContent = @file_get_contents($zipUrl);

        if ($zipContent === false) {
            $this->error("ZIP-Datei konnte nicht heruntergeladen werden: $zipUrl");
            return self::FAILURE;
        }

        file_put_contents($zielPfad, $zipContent);

        $this->info("ZIP erfolgreich gespeichert: $zielPfad");
        return self::SUCCESS;

        $this->ask('ENTER drücken zum Fortfahren');
        sleep(50);
    }

}
