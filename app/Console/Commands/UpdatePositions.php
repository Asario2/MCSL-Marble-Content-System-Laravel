<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePositions extends Command
{
    protected $signature = 'update:positions {table}';
    protected $description = 'Setzt eindeutige Positionen für eine Tabelle (ohne image_categories_id=3).';

    public function handle()
    {
        $table = "dagidag.".$this->argument('table');

        // Hole alle vorhandenen Positionen
        $existing = DB::table($table)

            ->whereNotNull('position')
            ->pluck('position')
            ->toArray();

        $usedPositions = collect($existing)->map(fn($p) => (int)$p)->unique()->sort()->values();

        // Startwert: 1
        $position = 1;

        // Hole alle Einträge ohne Position (die repariert werden müssen)
        $rows = DB::table($table)
            ->orderBy('ordering',"DESC")
            ->get();

        foreach ($rows as $row) {
            // solange Position schon vergeben ist oder im verbotenen Bereich 166–177 liegt → hochzählen
            //  && ($position >= 166 && $position <= 177)
            while ($usedPositions->contains($position)) {
                $position++;
            }

            // update
            DB::table($table)
                ->where('id', $row->id)
                // ->whereNot("image_categories_id","3")
                ->update(['position' => $position]);

            $this->info("ID {$row->id} -> Position $position");

            $usedPositions->push($position); // merken, dass diese Position jetzt belegt ist
            $position++;
        }

        $this->info("Positionen erfolgreich korrigiert!");
    }
}
