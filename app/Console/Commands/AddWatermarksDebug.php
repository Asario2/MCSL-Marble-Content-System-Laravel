<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AddWatermarksDebug extends Command
{
    protected $signature = 'watermark:add-debug
        {path}
        {table}
        {column}
        {watermark}
        {--imgdir=}';

    protected $description = 'DEBUG: Fügt Watermarks zu Bildern hinzu und zeigt warum ggf. nichts passiert';

    public function handle()
    {
        $path      = $this->argument('path');
        $table     = $this->argument('table');
        $column    = $this->argument('column');
        $watermark = $this->argument('watermark');
        $imgdir    = $this->option('imgdir');

        $this->line("📂 INPUT DIR: {$path}");
        $this->line("📄 TABLE: {$table}");
        $this->line("📄 COLUMN: {$column}");
        $this->line("🖼 WATERMARK: {$watermark}");
        $this->line("📁 IMGDIR: " . ($imgdir ?: 'NULL'));
        $this->line(str_repeat('-', 60));

        if (!File::exists($path)) {
            $this->error("❌ Ordner existiert nicht!");
            return Command::FAILURE;
        }

        $files = File::files($path);
        $this->info("🔍 Dateien gefunden: " . count($files));

        if (count($files) === 0) {
            $this->warn("⚠️ Ordner ist leer");
            return Command::SUCCESS;
        }

        foreach ($files as $file) {
            $this->line("\n➡️ Verarbeite: " . $file->getFilename());

            if (!in_array(strtolower($file->getExtension()), ['jpg','jpeg','png','webp'])) {
                $this->warn("⏭️ Kein Bild → übersprungen");
                continue;
            }

            if (!File::exists($file->getRealPath())) {
                $this->error("❌ Datei nicht lesbar");
                continue;
            }

            // UploadedFile erzeugen
            $mime = File::mimeType($file->getRealPath());

            $uploaded = new UploadedFile(
                $file->getRealPath(),
                $file->getFilename(),
                $mime,
                null,
                true // test mode (sehr wichtig!)
            );

            $this->line("📦 UploadedFile:");
            $this->line("  - originalName: " . $uploaded->getClientOriginalName());
            $this->line("  - mime: " . $uploaded->getMimeType());
            $this->line("  - size: " . $uploaded->getSize());

            // Fake Request
            $request = new \Illuminate\Http\Request();
            $request->files->set('image', $uploaded);

            $payload = [
                'table'       => $table,
                'column'      => $column,
                'iswatermark' => '1',
                'copyleft'    => $watermark,
                'ulpath'      => $imgdir ?? '',
                'is_imgdir'   => $imgdir ?? '',
                'Message'     => '0',
                "oripath"     => "0",
                "orifileName" => true,
            ];

            $request->merge($payload);

            $this->line("📨 Request Payload:");
            foreach ($payload as $k => $v) {
                $this->line("  {$k} => " . ($v === '' ? 'EMPTY' : $v));
            }

            try {
                $controller = App::make(\App\Http\Controllers\ImageUploadController::class);

                $response = $controller->upload($request, $table,1, 0,1);

                $this->line("📤 Response Status: " . $response->getStatusCode());
                $this->line("📤 Response Body: " . $response->getContent());

                Log::info("WATERMARK DEBUG OK", [
                    'file' => $file->getFilename(),
                    'response' => $response->getContent()
                ]);

            } catch (\Throwable $e) {
                $this->error("💥 EXCEPTION:");
                $this->error($e->getMessage());

                Log::error("WATERMARK DEBUG ERROR", [
                    'file' => $file->getFilename(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $this->line(str_repeat('-', 60));
        }

        $this->info("✅ DEBUG RUN DONE");
        return Command::SUCCESS;
    }
}
