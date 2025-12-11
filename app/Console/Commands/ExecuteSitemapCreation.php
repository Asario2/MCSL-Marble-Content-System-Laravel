<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Blog;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecuteSitemapCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate_old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically Generate an XML Sitemap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create();
        $base = 'https://www.asario.de/';
        $this->base = $base;
        //
        $sitemap->add(Url::create($base.'home/pictures')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.9));
        //
        $sitemap->add(Url::create($base.'didyouknow')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.7));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Acryl')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/3DPrinter')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Alphabet')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Ausgemaltes')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Digital')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Fasermaler')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Fineliner')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Keramik')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Lampen')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Monstarz')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Photos')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Sketches')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/show/pictures/Zentangles')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'home/users')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));
        //
        $sitemap->add(Url::create($base.'shortpoems')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.7));
        //
        Blog::whereDate('blog_date', '<=', Carbon::now())->get()->each(function (Blog $blog) use ($sitemap) {
            $sitemap->add(
                Url::create("{$this->base}blogs/show/{$blog->autoslug}")
                    ->setLastModificationDate(Carbon::yesterday())
                    ->setPriority(0.9)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        });
        //
        $sitemap->writeToFile(public_path('sitemap.xml'));
        //
        // Log::info('ExecuteSitemapCreation');
        //
        $this->newLine();
        $this->info('Der Befehl sitemap:generate wurde ausgeführt.');
        //
        return 0;
    }
}
