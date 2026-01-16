<?php

namespace Database\Seeders;

use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Blog;
use App\Models\User;
use App\Models\BlogImage;
use App\Models\BlogAuthor;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CreateBlogCategories::class);
        $this->call(CreateBlogImages::class);
        $this->call(CreateBlogAuthors::class);
        $this->call(CreateBlogWithMarkdownFormat::class);

         // nur bei der ersten Initialisierung
         if (1 == 1) {
            $this->call(AdminSeeder::class);
        }
    }
}

class CreateBlogCategories extends Seeder
{
    public function run()
    {
        BlogCategory::truncate();
        // Create BlogCategory
        BlogCategory::create([
            'id' => 1,
            'name' => 'Laravel',
        ]);
        // Create BlogCategory
        BlogCategory::create([
            'id' => 2,
            'name' => 'Vue',
        ]);
        // Create BlogCategory
        BlogCategory::create([
            'id' => 3,
            'name' => 'Tailwind CSS',
        ]);
        // Create BlogCategory
        BlogCategory::create([
            'id' => 4,
            'name' => 'SaaS-Anwendungen',
        ]);
    }
}

class CreateBlogImages extends Seeder
{
    public function run()
    {
        BlogImage::truncate();
        // Create BlogImage
        BlogImage::create([
            'id' => 1,
            'name' => 'Karteikartenschrank',
            'url' => '/images/blogimages/Blog_Karteikartenschrank_480x360.jpg',
        ]);
        // Create BlogImage
        BlogImage::create([
            'id' => 2,
            'name' => 'Computerbildschirm mit Code',
            'url' => '/images/blogimages/Blog_Screen_480x360.jpg',
        ]);
        // Create BlogImage
        BlogImage::create([
            'id' => 3,
            'name' => 'Idee',
            'url' => '/images/blogimages/Blog_Idee_480x360.jpg',
        ]);
    }
}

class CreateBlogAuthors extends Seeder
{
    public function run()
    {
        BlogAuthor::truncate();
        // Create BlogAuthor
        BlogAuthor::create([
            'id' => 1,
            'name' => 'Oliver Reinking',
            'info' => '
              Oliver Reinking ist ein äußerst erfahrener Diplom-Mathematiker.
              <br />
              Oliver ist ein angesehener Autor von Sachbüchern und hat bereits Bücher über
              Laravel, Vue, ChatGPT und Business Intelligence geschrieben.
              <br />
              <br />
              Seine Kontaktinformationen lauten:
              <br />
              Oliver Reinking, Nordpfad 25, 66482 Zweibrücken, Mail: oliver@codingjungle.de
            ',
        ]);
    }
}

// Blogartikel im Markdown-Format
class CreateBlogWithMarkdownFormat extends Seeder
{
    public function run()
    {
        Blog::truncate();
        // für den ersten Artikel setze markdown_on auf true
        $blog_author_id = 1;
        $blog_image_id = 1;
        $blog_category_id = 1;
        $title = "Laravel 11 - Kickstart für deine SaaS-Anwendungen";
        $summary = "Im Fachbuch 'Laravel 11 - Dein Startschuss für SaaS-Anwendungen' führt der Autor Oliver Reinking detailliert durch ein Laravel-Template, das als Grundlage für die Entwicklung von SaaS-Anwendungen dient.";
        $blog_date = "2024-04-01";
        $reading_time = 4;
        //
        Blog::create([
            'blog_author_id' => $blog_author_id,
            'blog_image_id' => $blog_image_id,
            'blog_category_id' => $blog_category_id,
            'title' => $title,
            'summary' => $summary,
            'blog_date' => $blog_date,
            'content' => null,
            'reading_time' => $reading_time,
            'markdown_on' => true,
        ]);
    }
}

class AdminSeeder extends Seeder
{
    public function run()
    {
         // Anwender
         User::create([
            'first_name' => 'Oliver',
            'last_name' => 'Reinking',
            'email' => 'info@starter-eleven.de',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('12345678'),
            'is_admin' => true,
            'is_employee' => true,
            'is_customer' => true,
        ]);
    }
}

