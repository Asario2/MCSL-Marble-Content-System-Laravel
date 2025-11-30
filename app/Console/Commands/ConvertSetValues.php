<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertSetValues extends Command
{
    protected $signature = 'mcsl:convert-set';

    protected $description = 'Konvertiert SET-Werte ab, dag, mfx zu ab_mcsl, dag_mcsl, mfx_mcsl und lässt alle anderen Werte unverändert.';

    public function handle()
    {
        $table = "genxlo.privacy";
        $column = "xico_doms";

        $this->info("Konvertiere SET-Werte in {$table}.{$column} …");

        DB::table($table)->orderBy('id')->chunk(200, function ($rows) use ($table, $column) {

            foreach ($rows as $row) {

                $value = $row->{$column};

                if ($value === null || $value === '') {
                    continue;
                }

                // SET-Werte kommen als "ab,xyz,mfx"
                $values = explode(',', $value);

                // Mapping durchführen
                $mapped = array_map(function ($v) {
                    return match ($v) {
                        'ab' => 'ab_mcsl',
                        'dag' => 'dag_mcsl',
                        'mfx' => 'mfx_mcsl',
                        default => $v, // NICHT verändern
                    };
                }, $values);

                // Wieder zusammensetzen zur SET-Notation
                $newValue = implode(',', $mapped);

                if ($newValue !== $value) {
                    DB::table($table)->where('id', $row->id)->update([
                        $column => $newValue
                    ]);
                }
            }
        });

        $this->info("✔ Konvertierung abgeschlossen.");
    }
}
