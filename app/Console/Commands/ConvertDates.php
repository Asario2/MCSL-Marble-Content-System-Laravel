<?php

// app/Console/Commands/ConvertDates.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\GlobalController;


class ConvertDates extends Command
{
    protected $signature = 'dates:convert';
    protected $description = 'Convert date and chg_date to created_at and updated_at';

    public function handle()
    {
        GlobalController::SetDomain();
        $table = "ratings";
        // Beispiel: Tabelle "your_table_name"
        $rows = DB::table($table)->get();

        foreach ($rows as $row) {
            // Konvertiere date & chg_date zu Carbon Instanzen
            $createdAt = $row->date ? Carbon::parse($row->date)->format('Y-m-d H:i:s') : now();
            $updatedAt = $row->chg_date ? Carbon::parse($row->chg_date)->format('Y-m-d H:i:s') : now();

            DB::table($table)
                ->where('id', $row->id)
                ->update([
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

            $this->info("Updated row ID {$row->id}");

            }
        $this->info('Conversion complete.');
    }
}
