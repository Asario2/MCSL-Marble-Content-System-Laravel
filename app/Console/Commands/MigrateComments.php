<?php

            namespace App\Console\Commands;

            use Illuminate\Console\Command;
            use Illuminate\Support\Facades\DB;
            use Carbon\Carbon;

            class MigrateComments extends Command
            {
                protected $signature = 'comments:migrate';
                protected $description = 'Migrate old comments from devlog, news and mindblog into the comments table';

                public function handle()
                {
                    DB::statement('TRUNCATE TABLE comments');
                    $this->info('Migrating comments...');
                    $this->migrateFrom_alt('ffrog_images_comments', 'ffrog_news', 'news_orig', 'images_id','8');
                    $this->migrateFrom_alt('ffrog_shortpoems_comments', 'ffrog_news', 'news_orig', 'shortpoems_id','10');
                    $this->migrateFrom_alt('ffrog_didyouknow_comments', 'ffrog_news', 'news_orig', 'didyouknow_id','6');
                    $this->migrateFrom('ffrog_news_comments', 'ffrog_news', 'news_orig', 'news_id');
                    $this->migrateFrom('ffrog_devlog_comments', 'ffrog_devlog', 'devlog_orig', 'devlog_id');
                    $this->migrateFrom('ffrog_mindblog_comments', 'ffrog_mindblog', 'mindblog_orig', 'mindblog_id');

                    $this->info('Migration complete.');
                }

                private function migrateFrom_alt($commentTable, $postTable, $origTable, $pag = '',$adt='')
                {

                    $comments = DB::table($commentTable)->where('pub', 1)->get();

                    foreach ($comments as $comment) {
                        // 1. Headline aus news_orig / devlog_orig / mindblog_orig holen
                        // $headlineObj = DB::table($origTable)
                        //     ->where("id", $comment->$pag)
                        //     ->select("headline")
                        //     ->first();

                        // if (!$headlineObj || !$headlineObj->headline) {
                        //     \Log::warning("Keine Headline gefunden für {$pag}={$comment->$pag} in {$origTable}");
                        //     continue;
                        // }

                        // $headline = $headlineObj->headline;

                        // // 2. Blog-Eintrag mit passender Headline finden
                        // $blog = DB::table("blogs")
                        //     ->where("title", $headline)
                        //     ->select("id")
                        //     ->first();

                        // if (!$blog) {
                        //     \Log::warning("Kein Blog gefunden für Headline '{$headline}' in blogs");
                        //     continue;
                        // }

                        // 3. Datum konvertieren
                        $created = $this->parseDate($comment->date);
                        $updated = $this->parseDate($comment->chg_date);

                        // 4. Insert in neue Tabelle
                        DB::table('comments')->insert([
                            'post_id' => $comment->$pag,
                            'admin_table_id' => $adt,
                            'users_id' => $comment->users_id,
                            'content' => $comment->message,
                            'email' => $comment->email,
                            'created_at' => $created,
                            'updated_at' => $updated,
                            'ischecked' => 1,
                            "nick"=>$comment->nick,
                        ]);
                    }

                    $this->info("Imported from {$commentTable}");
                }
                private function migrateFrom($commentTable, $postTable, $origTable, $pag = '')
                {

                    $comments = DB::table($commentTable)->where('pub', 1)->get();

                    foreach ($comments as $comment) {
                        // 1. Headline aus news_orig / devlog_orig / mindblog_orig holen
                        $headlineObj = DB::table($origTable)
                            ->where("id", $comment->$pag)
                            ->select("headline")
                            ->first();

                        if (!$headlineObj || !$headlineObj->headline) {
                            \Log::warning("Keine Headline gefunden für {$pag}={$comment->$pag} in {$origTable}");
                            continue;
                        }

                        $headline = $headlineObj->headline;

                        // 2. Blog-Eintrag mit passender Headline finden
                        $blog = DB::table("blogs")
                            ->where("title", $headline)
                            ->select("id")
                            ->first();

                        if (!$blog) {
                            \Log::warning("Kein Blog gefunden für Headline '{$headline}' in blogs");
                            continue;
                        }

                        // 3. Datum konvertieren
                        $created = $this->parseDate($comment->date);
                        $updated = $this->parseDate($comment->chg_date);

                        // 4. Insert in neue Tabelle
                        DB::table('comments')->insert([
                            'post_id' => $blog->id,
                            'admin_table_id' => 3,
                            'users_id' => $comment->users_id,
                            'content' => $comment->message,
                            'email' => $comment->email,
                            'created_at' => $created,
                            'updated_at' => $updated,
                            'ischecked' => 1,
                            "nick"=>$comment->nick,
                        ]);
                    }

                    $this->info("Imported from {$commentTable}");
                }


                private function migrateFrom_old($commentTable, $postTable, $origTable, $pag = '')
                {
                    $comments = DB::table($commentTable)->where('pub', 1)->get();
                    $poa = str_replace("_id", '', $pag); // z. B. news

                    foreach ($comments as $comment) {
                        // 1. Hole headline_id aus ffrog_news|devlog|mindblog
                        $post = DB::table($origTable)->where('id', $comment->$pag)->first();
                        if (!$post || !$post->headline) continue;

                        // 2. Hole Headline aus *_orig (z. B. news_orig)
                        $headline = DB::table($origTable)
                            ->where('headline', $post->headline)
                            ->value('headline');

                        if (!$headline) continue;

                        // 3. Suche blogs.id mit gleicher Headline
                        $blog = DB::table('blogs')
                            ->where('headline', $headline)
                            ->first();

                        if (!$blog) {
                            \Log::warning("Kein Blog gefunden für Headline: '{$headline}' aus {$origTable}");
                            continue;
                        }

                        // 4. Datum konvertieren
                        $created = $this->parseDate($comment->date);
                        $updated = $this->parseDate($comment->chg_date);

                        // 5. Insert in neue Tabelle
                        DB::table('comments')->insert([
                            'post_id' => $blog->id,
                            'admin_table_id' => 3,
                            'users_id' => $comment->users_id,
                            'content' => $comment->message,
                            'email' => $comment->email,
                            'created_at' => $created,
                            'updated_at' => $updated,
                            'ischecked' => 1,
                            "nick"=>$comment->nick,
                        ]);
                    }

                    $this->info("Imported from {$commentTable}");
                }



    private function parseDate($dateStr)
    {
        // z. B. "15.05.2021 14:23:10"
       $dateStr = (int)$dateStr;
//         \Log::info("ds:".$dateStr);
        try {
            return date('Y-m-d H:i:s', $dateStr);
        } catch (\Exception $e) {
            return now(); // fallback
        }
    }
}
