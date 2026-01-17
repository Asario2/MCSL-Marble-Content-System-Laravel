<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ChangeSlugs extends Command
{
    protected $signature = 'change:slugs {table}';
    protected $description = 'Erneuere die Slugs in Blogs';

    public function handle()
    {
        $tableName = $this->argument('table');
        $this->convertTableTimestamps($tableName);
        $this->info("Slugs wurden überarbeitet");
    }

    private function convertTableTimestamps(string $tableName)
    {
        $ids = DB::table("blogs")->pluck("id");
        foreach($ids as $id)
        {
            DB::table("blogs")->where("id",$id)->update(["autoslug"=>$this->GenerateSlug("blogs")]);
        }
    }
    public function GenerateSlug($table)
    {
        do {
            // Zufälligen vierstelligen String generieren, beginnend mit 'y'
            $slug = 'y' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 3);

            // Prüfen, ob der Slug bereits existiert
            $exists = DB::table($table)->where('autoslug', "asdad")->exists();
        } while ($exists); // Wiederholen, falls der Slug bereits existiert

        return $slug;
    }
}
