<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateRatingsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan ratings:update-table
     */
    protected $signature = 'ratings:update-table';

    /**
     * The console command description.
     */
    protected $description = 'Update ratings.table with image_categories.name via images.image_categories_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Updating ratings.table ...");

        // Hole alle relevanten Daten
        $ratings = DB::table('ratings')
            ->join('images', 'ratings.images_id', '=', 'images.id')
            ->join('image_categories', 'images.image_categories_id', '=', 'image_categories.id')
            ->select('ratings.id as rating_id', 'image_categories.name as cat_name')
            ->get();

        $count = 0;
        foreach ($ratings as $rating) {
            DB::table('ratings')
                ->where('id', $rating->rating_id)
                ->update(['table' => $rating->cat_name]);
            $count++;
        }

        $this->info("Done. Updated {$count} rows.");

        return Command::SUCCESS;
    }
}
