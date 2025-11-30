<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReorderNews extends Command
{
    protected $signature = 'news:reorder';
    protected $description = 'Sortiert news nach created_at DESC und setzt position neu von 1-x';

    public function handle()
    {
        $this->info("Starte Neu-Sortierung...");

        // Alle News holen, newest first
        $news = DB::table('genxlo.privacy')
            ->orderBy('position', 'DESC')
            ->get();

        $position = 1;

        foreach ($news as $item) {
            DB::table('genxlo.privacy')
                ->where('id', $item->id)
                ->update(['position' => $position]);

            $this->line("ID {$item->id} → Position {$position}");
            $position++;
        }

        $this->info("Fertig! {$position}-1 Einträge wurden aktualisiert.");
        return 0;
    }
}
