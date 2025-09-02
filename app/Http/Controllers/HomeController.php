<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Blog;
use Inertia\Inertia;
use App\Models\DidYouKnow;
use App\Models\ShortPoem;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\RatingController;
use App\Models\Tenant;  // <-- hier Tenant importieren
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use League\CommonMark\CommonMarkConverter;
if(!session_id())
{
    session_start();
}
class HomeController extends Controller
{
    //
    public function home_index_old()
    {
        //
        $success = 'Herzlichen Willkommen im Laravel-Template Starter Eleven';
        Session::flash('toast.info', $success);
        //
        return Inertia::render('Homepage/Home');
    }
    public function home_AI()
    {
        $data = DB::table("texts")->select('texts.*','users.name as author_name')->leftJoin('users', 'users.id', '=', 'texts.users_id')->where("texts.id","9")->first();
        $data->text = Str::markdown($data->text);
        return Inertia::render('Homepage/AiContent', ["data" => [$data]]); // <-- in Array umwandeln
    }
    public function home_index(Request $request)
    {
        $subdomain = SD();
        if($subdomain == "ab"){
            return $this->home_blog_index($request);
        }
        else{
            $ho = "home_".$subdomain;
            return $this->$ho();
        }

    }
    //
    public function home_blog_index(Request $request)
    {
    //     $host = request()->getHost();
    //     $subdomain = explode('.', $host)[0];
    //     $tenant = Tenant::where('subdomain', $subdomain)->firstOrFail();

    //     // Dynamische DB-Verbindung setzen
    //     config(['database.connections.mysql.database' => $tenant->database]);
    //     DB::purge('mysql');
    //     DB::reconnect('mysql');
    //     // \Log::info('Subdomain gefunden: ' . $subdomain);
    //     // \Log::info('Tenant gefunden: ' . $tenant->database);

        $zeitpunkt = Carbon::now();
        //
        $blogs = Blog::select(
            'blogs.id as id',
            'blogs.blog_date as blog_date',
            'blogs.title as title',
            'blogs.title as name',
            'blogs.autoslug as autoslug',
            'blogs.summary as summary',
            'blogs.reading_time as reading_time',
            'blog_authors.name as author_name',
            'blogs.image_path as url',
            'blog_categories.name as category_name',
            "blogs.xis_aiImage as madewithai"
            )
            ->join('blog_authors', 'blog_authors.id', '=', 'blogs.blog_authors_id')
            ->join('blog_categories', 'blog_categories.id', '=', 'blogs.blog_categories_id')
            //
            ->whereDate('blog_date', '<=', $zeitpunkt)
            ->where("blogs.pub","1")
            ->orWhere("blogs.pub","2")
            //
            ->filterBlog(Request::only('search'))
            ->orderBy('blogs.blog_date', 'desc')
            ->orderBy('blogs.id', 'desc')
            ->paginate(19)
            ->withQueryString();
        //
        $blogs->getCollection()->transform(function ($blog) {
            $blog->summary = KILLMD($blog->summary);
            return $blog;
        });
        $blogs->getCollection()->transform(function ($blog) {
            $blog->title = RUMLAUT($blog->title);
            return $blog;
        });

        $blogs->getCollection()->transform(function ($blog) {
            $blog->title = html_entity_decode($blog->title);
            return $blog;
        });

        $table = "blogs";
        $blogs->aiOverlayImage = "ai-".@$_SESSION['dm'].".png";
        // foreach ($blogs as $blog) {
        //     $blog->editRights = CheckRights(Auth::id(), $table, "edit");
        //     $blog->viewRights = CheckRights(Auth::id(), $table, "view");
        //     $blog->deleteRights = CheckRights(Auth::id(), $table, "delete");
        //     $blog->addRights = CheckRights(Auth::id(), $table, "add");
        //     $blog->date_tableRights = CheckRights(Auth::id(), $table, "date_table");
        // }
            // \Log::info(json_encode($blogs, JSON_PRETTY_PRINT));
            // \Log::info("Test-Log-Eintrag");

        return Inertia::render('Homepage/BlogList', [
            'filters' => Request::all('search'),
            'blogs' => $blogs,
        ]);
    }
    public function home_images_index()
    {
        $table = "image_categories";
        $data = DB::table($table)->orderBy("shortname","ASC")->get();
        return Inertia::render('Homepage/PicturesCat', [
           'data'=>$data,

        ]);
    }
    public function home_images_search_cat(Request $request)
    {
        // if($slug != "Alphabet")
        // {
        //     $ord[0] = "created_at";
        //     $ord[1] = "DESC";
        // }
        // else{
            $ord[0] = "created_at";
            $ord[1] = "DESC";
//         // }
        // \Log::info(Request::input('search')); // oder: \Log::info($request->all());
// ;
//         $search = $request->search;
        $search = Request::input('search');   // ← korrekt für die Facade
            if(empty($search))
            {
                header("Location: /home/pictures");
            }

    // $entries = Image::query()
    // ->leftJoin("image_categories", "image_categories.id", "=", "images.image_categories_id")
    // ->whereIn('images.pub', [1, 2])                    // Sichtbarkeit
    // ->select("images.created_at AS created_at , images.*, image_categories.*")
    // ->filterDefault(['search' => $search])      // ← eigener Scope
    // ->orderByDesc('images.created_at')                 // Sortierung
    // ->paginate(20);
    // $entries = DB::table('images')
    // ->leftJoin('image_categories', 'image_categories.id', '=', 'images.image_categories_id')
    // ->whereIn('images.pub', [1, 2])
    // ->when($search, function ($q, $s) {
    //     $q->where('image_categories.shortname', 'like', "%{$s}%")
    //       ->orWhere('images.headline', 'like', "%{$s}%")
    //       ->orWhere('images.created_at', 'like', "%{$s}%");
    // })
    // ->select(
    //     'images.*',
    //     'images.created_at as created_at',
    //     'image_categories.slug as slug'
    // )
    // ->orderByDesc('images.created_at')
    // ->paginate(20);
    $rat = RatingController::getTotalRating("images");
    $perPage = 25;

    $query = Image::published()
        ->when(request('search'), function ($query) {
            $query->where(function ($q) {
                $q->filterdefault(['search' => request('search')]);
            });
        })
        ->orderBy('created_at', 'desc');

    $paginated = $query->paginate($perPage);

    $data = [
        'data' => $paginated->items(), // die aktuellen Items
        'ocont' => [],
        'links' => $paginated->linkCollection(), // automatisch generierte Links
        'meta' => [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
            'total' => $paginated->total(),
        ],
    ];
    $rat = RatingController::getTotalRating("images");

    return Inertia::render('Homepage/Pictures', [
        'items' => $data,
        'ratings' => $rat,
        "entries"=>$data, // falls du Bewertungen dazupackst
        "filters" => request()->only('search'),
    ]);          // ← dann paginierenp
\Log::info(json_encode($entries));
return Inertia::render('Homepage/Pictures', [
    'entries' => $entries,
    'filters' => ['search' => $search],
    'ratings' => RatingController::getTotalRating('images'),
]);
}
    public function home_images(Request $request,$slug)
    {
        if($slug != "Alphabet")
        {
            $ord[0] = "created_at";
            $ord[1] = "DESC";
        }
        else{
            $ord[0] = "position";
            $ord[1] = "ASC";
        }
        $search = Request::input('search');   // ← korrekt für die Facade
        $entries = DB::table("images")
            ->leftJoin("image_categories", "image_categories.id", "=", "images.image_categories_id")
            ->whereIn("images.pub", [1, 2])
            ->where("image_categories.slug",$slug)
            ->select("images.created_at AS created_at", "images.*", "image_categories.slug as slug")

            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where("images.headline", "like", "%{$search}%")
                      ->orWhere("images.message", "like", "%{$search}%")
                      ->orWhere("images.created_at", "like", "%{$search}%");
                });
            })
            ->orderByDesc("images.created_at")
            ->paginate(20);

        // \Log::info("cr:".CheckRights(Auth::id(),"images","date"));
        $rat = RatingController::getTotalRating("images");
        // \Log::info("en:" . json_encode([$rat]));
        $ocont = DB::table("image_categories")->where("slug",$slug)->first();
        return Inertia::render('Homepage/Pictures', [
            'entries' => $entries,
            'ocont' => $ocont,
            'filters' => Request()->all('search'),
            'ratings' => $rat,
        ]);
    }
    public function home_images_show_mfx($id)
    {
        $images = DB::table('images')
            ->where('id', $id)
            ->whereIn('pub', [1, 2])

            ->first();

        $def = str_replace("_mfx/images/pix/",'',@$images->imgdir_content)   ;
        $im_cont = readtitlez(public_path("/images/_".SD()."/images/imgdir_content/".$def."/index.json"));

        $im_cont = @file_get_contents(public_path("/images/_".SD()."/images/imgdir_content/".$def."/index.json"));
        $im_cont = json_decode($im_cont);
        // dd($im_cont);

        return Inertia::render('Homepage/mfx/ImagesShow', ["images"=>$images,"im_cont"=>$im_cont,"def"=>$def]);
    }
    public function home_images_cat_mfx(Request $request)
    {
        // if($slug != "Alphabet")
        // {
        //     $ord[0] = "created_at";
        //     $ord[1] = "DESC";
        // }
        // else{
        //     $ord[0] = "position";
        //     $ord[1] = "ASC";
        // }
        $search = Request::input('search');   // ← korrekt für die Facade
        $entries = DB::table("images")
            ->whereIn("images.pub", [1, 2])
            ->select("images.created_at AS created_at", "images.*")

            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where("images.headline", "like", "%{$search}%")
                      ->orWhere("images.message", "like", "%{$search}%")
                      ->orWhere("images.created_at", "like", "%{$search}%");
                });
            })

            ->orderBy("position")
            ->paginate(20);

        // \Log::info("cr:".CheckRights(Auth::id(),"images","date"));
        // $rat = RatingController::getTotalRating("images");
        // \Log::info("en:" . json_encode([$rat]));
        // $ocont = DB::table("image_categories")->where("slug",$slug)->first();
        return Inertia::render('Homepage/mfx/images_cat', [
            'data' => $entries,
            // 'ocont' => $ocont,
            'filters' => Request()->all('search'),
            // 'ratings' => $rat,
        ]);
    }

    public function home_index_alt(){
        $sd = SD();
        if($sd == "ab"){
            dd($sd);
            return $this->home_index();
        }
        else{
            $er = "home_$sd";
            return $this->$er();
        }
    }
    //
    public function home_shortpoems(Request $request)
    {
        $rat = RatingController::getTotalRating("shortpoems");
        $perPage = 25;

        $query = ShortPoem::published()
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->filterdefault(['search' => request('search')]);
                });
            })
            ->orderBy('created_at', 'desc');

        $paginated = $query->paginate($perPage);

        $data = [
            'data' => $paginated->items(), // die aktuellen Items
            'links' => $paginated->linkCollection(), // automatisch generierte Links
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
            ],
        ];
        $rat = RatingController::getTotalRating("shortpoems");
        // $data = $data->map(function ($item) {
        //     $item->story = html_entity_decode(html_entity_decode($item->story));
        //     $item->headline = html_entity_decode(html_entity_decode($item->headline));
        //     return $item;
        // });
        // $data = $paginated->toArray()['data'];
        // $data = collect($data)->map(function ($item) {
        //     $item['story'] = html_entity_decode(html_entity_decode($item['story']));
        //     $item['headline'] = html_entity_decode(html_entity_decode($item['headline']));
        //     return $item;
        // });
        $data = $paginated->through(function ($item) {
            $item->story = html_entity_decode(html_entity_decode($item->story));
            $item->headline = html_entity_decode(html_entity_decode($item->headline));
            return $item;
        });
        return Inertia::render('Pages/shortpoems', [
            'items' => $data,
            'ratings' => $rat, // falls du Bewertungen dazupackst
            "filters" => request()->only('search'),
        ]);          // ← dann paginierenp
        // \Log::info("VALS:".json_encode($values));
        // $values = DB::table("shortpoems")->select('id', 'headline', 'story')->get();
        // return Inertia::render('Homepage/Shortpoems', [
        //     // 'items' => $values,
        //     'entries' => [['headline' => 'Test', 'story' => 'Nur ein Test']],
        // ]);



        return Inertia::render('Pages/Shortpoems', [
            'items' => $values,
            'ratings' => $rat,
        ]);
    }
    public function home_didyouknow()
    {
        // $ord[0] = "created_at";
        // $ord[1] = "DESC";
        // $values = DB::table("didyouknow")->orderBy("created_at","DESC")->where(function ($query) {
        //     $query->where("didyouknow.pub", 1)
        //           ->orWhere("didyouknow.pub", 2);
        // })->when(request("search"), function ($query){
        //     $query->where(function ($subquery) {
        //         $subquery->filterdefault(['search' => request('search')]);
        //     });
        // })
        // ->orderBy($ord[0], $ord[1])
        // ->paginate(25);


        // $values = DidYouKnow::published()
        // ->when(request('search'), function ($query) {
        //     $query->where(function ($q) {
        //         $q->filterdefault(['search' => request('search')]);
        //     });
        // })
        // ->orderBy('created_at', 'desc')
        // ->paginate(25);
        $perPage = 25;

        $query = DidYouKnow::published()
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->filterdefault(['search' => request('search')]);
                });
            })
            ->orderBy('created_at', 'desc');

        $paginated = $query->paginate($perPage);

        $data = [
            'data' => $paginated->items(), // die aktuellen Items
            'links' => $paginated->linkCollection(), // automatisch generierte Links
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
            ],
        ];
        $rat = RatingController::getTotalRating("didyouknow");
        return Inertia::render('Pages/didyouknow', [
            'items' => $data,
            'ratings' => $rat, // falls du Bewertungen dazupackst
            "filters" => request()->only('search'),
        ]);

        // ← dann paginierenp

        // \Log::info("VALS:".json_encode($values));
        // $values = DB::table("shortpoems")->select('id', 'headline', 'story')->get();
        // return Inertia::render('Homepage/Shortpoems', [
        //     // 'items' => $values,
        //     'entries' => [['headline' => 'Test', 'story' => 'Nur ein Test']],
        // ]);

        $values = $values->map(function ($item) {
            $item->answer = html_entity_decode($item->answer);
            $item->headline = html_entity_decode($item->headline);
            return $item;
        });
        \Log::info("SQL:".json_encode($values));
        return Inertia::render('Pages/didyouknow', [
            'items' => $values,
                'ratings' => $rat,
        ]);
    }
    public function home_images_search(Request $request,$slug)
    {
        if($slug != "Alphabet")
        {
            $ord[0] = "created_at";
            $ord[1] = "DESC";
        }
        else{
            $ord[0] = "position";
            $ord[1] = "ASC";
        }

        DB::enableQueryLog();
        $entries = DB::table("images")
        ->leftJoin("camera", "images.camera_id", "=", "camera.id")
        ->leftJoin("image_categories", "image_categories.id", "=", "images.image_categories_id")
        ->select("images.*", "camera.name as camera", "image_categories.slug as category_slug")
        ->where("image_categories.slug", "$slug") // 🔹 Begrenzung auf die Kategorie
        ->where(function ($query) {
            $query->where("images.pub", 1)
                  ->orWhere("images.pub", 2);
        })->when(request("search"), function ($query) use ($slug) {
            $query->where(function ($subquery) {
                $subquery->filterdefault(['search' => request('search')]);
            });
        })
        ->orderBy($ord[0], $ord[1])
        ->paginate(25);             // ← dann paginierenp

        $queries = DB::getQueryLog();
        //\Log::info  ("QQ:".json_encode($queries));
        $rat = RatingController::getTotalRating("images");
        $ocont = DB::table("image_categories")->where("slug",$slug)->first();
        return Inertia::render('Homepage/Pictures', [
            'entries' => $entries,
            'ocont' => $ocont,
            'filters' => Request()->all('search'),
            'ratings' => $rat,
        ]);
    }
    public function no_rights()
    {
        $text = DB::table("texts")->where("type", "no-rights")->select('headline', 'text')->first();
        // \Log::info("TT:".json_encode($text));

        return Inertia::render('Homepage/No-Rights', [
            "texts" => $text  // Übergebe das Objekt direkt, nicht als JSON-String
        ]);
    }
    //
    public function home_blog_show($autoslug)
    {
        $blog = Blog::where('autoslug', $autoslug)->where("pub","1")
        ->orWhere("pub","2")->first();
        //
        if(empty($blog)){
            header("Location: /blogs");
            exit;
        }
        $blog->madewithai = $blog->xis_aiImage;
        $blog->blog_author;
        // $blog->blog_images;
        $blog->url = $blog->image_path;
        $blog->blog_category;
        // $blog->editRights = CheckRights(Auth::id(),"blogs","edit");
        // $blog->deleteRights = CheckRights(Auth::id(),"blogs","delete");
        $blogarticle = null;
        //
        if ($blog->markdown_on) {

             $blogarticle = renderText($blog->content);
            //  \Log::info("md:".json_encode($blogarticle));
            $blog->content = renderText($blog->content);
            // $filePathName = 'blogs/blog_' . $blog->id . '.md';
            // $blogarticleFile = Jetstream::localizedMarkdownPath($filePathName);
            // $blogarticle = Str::markdown(file_get_contents($blogarticleFile));
        }

            $blog->content = RUMLAUT($blog->content);
            $blog->title = RUMLAUT($blog->title);

        //
        // \Log::info("bl: ".json_encode($blog));
        return Inertia::render('Homepage/BlogShow', [
            'blog' => $blog,
            'blogarticle' => $blogarticle
        ]);
    }
    public function home_userlist()
    {
        $search = Request::input('search');   // ← korrekt für die Facade
        $users = DB::table("users")->where("xis_disabled","0")->where("pub","1")
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where("users.name", "like", "%{$search}%")
                  ->orWhere("users.about", "like", "%{$search}%")
                  ->orWhere("users.created_at", "like", "%{$search}%");
            });
        })->select("users.*")->orderBy("name","ASC")->paginate(18);
        return Inertia::render('Homepage/Users', [
            'users' => $users, // statt 'data'
            'filters' => Request()->all('search'),
        ]);
    }
    //
    public function renderMarkdown(CommonMarkConverter $converter,$markdown)
    {

        $html = $converter->convert($markdown);

        return response()->json(['html' => $html]);
    }
    public function home_get_started()
    {
        $getStartedFile = Jetstream::localizedMarkdownPath('get_started.md');
        $getStarted = Str::markdown(file_get_contents($getStartedFile));
        //
        return Inertia::render('Homepage/GetStarted', [
            'get_started' => $getStarted,
        ]);
    }
    //
    public function home_pricing()
    {
        return Inertia::render('Homepage/Pricing');
    }
    //
    public function home_imprint()
    {
        $imprintFile = Jetstream::localizedMarkdownPath('imprint.md');
        $imprint = Str::markdown(file_get_contents($imprintFile));
        //
        return Inertia::render('Homepage/Imprint', [
            'imprint' => $imprint,
        ]);
    }
    public function changelog_old(){
        $data = DB::table("texts")->select('texts.*','users.name as author_name')->leftJoin('users', 'users.id', '=', 'texts.users_id')->where("texts.id","15F")->first();
        $data->text = Str::markdown($data->text);
        return Inertia::render('Homepage/Changelog_old', ["data" => [$data]]); // <-- in Array umwandeln

    }
    //
    public function home_privacy()
    {
        // if(SD() != "ab")
        // {
            $set = "_".SD();
            $dol = SD()."/";
        // }
        $pf = "privacy".@$set.".md";
        $privacyFile = Jetstream::localizedMarkdownPath($pf);
        include_once "inc/functions/rinfo_code.php";
        // dd($privacyFile);
        $privacy = Str::markdown(file_get_contents($privacyFile)); // HTML erzeugt
        $privacy = rinfo_code($privacy);
        // $privacy = nl2br($privacy);
        return Inertia::render('Homepage/'.@$dol.'Privacy', [
            'privacy' => $privacy,
        ]);
    }

    public function home_terms()
    {
        $termsFile = Jetstream::localizedMarkdownPath('terms.md');
        $terms = Str::markdown(file_get_contents($termsFile));
        //
        return Inertia::render('Homepage/Terms', [
            'terms' => $terms,
        ]);
    }


    public function home_no_application_found()
    {
        return redirect('/');
    }

    public function home_user_is_no_admin()
    {
        return redirect('/');

    }

    public function home_user_is_no_employee()
    {
        return redirect('/');

    }

    public function home_user_is_no_customer()
    {
        return redirect('/');

    }

    public function home_invalid_signature()
    {
        return Inertia::render('Homepage/InvalidSignature');
    }

    public function home_rindex(){
        if(SD() != "ab"){
            return $this->home_ri();
        }
        return Inertia::location('/');
    }
    public function home_ri(){
        return Inertia::render("Homepage/ri");
    }
    public function home_mfx()
    {
        //
        $success = 'Herzlichen Willkommen im Laravel-Template Starter Eleven';
        Session::flash('toast.info', $success);
        //
        $news = DB::table("news")->orderBy("id", "DESC")->paginate(10);
        $text = DB::table("texts")->where("type","mfx_welcome")->select("headline","text")->first();
        // dd($text);


        return Inertia::render('Homepage/mfx/Home', compact('news', 'text'));
    }
    public function people()
    {
        $data = DB::table("users")->where("pub","1")->where("xis_friends","1")->orderBy("users_rights_id","ASC")->get();
        return Inertia::render('Homepage/mfx/people',compact('data'));
    }
    public function infos_index(){
        $data = DB::table("infos")->where("pub","1")->select("id","headline","summary","img_thumb")->orderBy("position","DESC")->get();
        return Inertia::render('Homepage/mfx/infos_cat',compact('data'));
    }
    public function infos_privacy()
    {
        return $this->infos_show("43");
    }
    public function contacts_mfx(){
        $text = DB::table("texts")->where("type", "ContactsHeader")->select('headline', 'text')->first();
        $contacts = DB::table("texts")->where("type", "ContactsInfos")->select('headline', 'text')->first();
        // \Log::info("TT:".json_encode($text));

        return Inertia::render('Homepage/mfx/contacts', [
            "text" => $text,
            'contacts'=>$contacts,
        ]);
    }
    public function infos_show($id){
        $data = DB::table("infos")->where("pub","1")->where("id",$id)->select("id","headline","message","img_big")->orderBy("position","DESC")->first();
        if(SD() != "ab")
        {
            $set = "_".SD();
            $dol = SD()."/";
        }
        $pf = "privacy".@$set.".md";
        $privacyFile = Jetstream::localizedMarkdownPath($pf);
        include_once "inc/functions/rinfo_code.php";
        $privacy = Str::markdown(file_get_contents($privacyFile)); // HTML erzeugt
        $privacy = rinfo_code($privacy);
        return Inertia::render('Homepage/mfx/infos_show',compact('data',"privacy"));
    }
    public function infos_pow($id='48'){
        $data = DB::table("infos")->where("id",$id)->select("id","headline","message","img_big")->orderBy("position","DESC")->first();
        return Inertia::render('Homepage/mfx/infos_show',compact('data'));
    }
    public function getVotez()
    {
        $data = DB::table('votez')->where("pub","1")->get();
        return response()->json($data);
    }
    public function imprint($id = '45')
    {
        $data = DB::table("infos")->where("pub","1")->where("id",$id)->select("id","headline","message","img_big")->orderBy("position","DESC")->first();
        return Inertia::render('Homepage/mfx/infos_show',compact('data'));
    }
    public function projects()
    {
        $data = DB::table("projects")->where("pub","1")->select("id","name","created_at","Umfang","img_thumb","url","xis_mcs","xis_mcsl")->orderBy("created_at","DESC")->get();
        $sheets = DB::table("projects_sheets")
    ->leftJoin("users", "projects_sheets.users_id", "=", "users.id")
    ->select("projects_sheets.projects_id", "users.name as nick", "projects_sheets.Aufgabe")
    ->get()
    ->groupBy('projects_id')
    ->map(function($group) {
        return $group->map(function($item) {
            return [
                'nick' => $item->nick,
                'aufgabe' => $item->Aufgabe
            ];
        });
    });

        return Inertia::render('Homepage/mfx/projects',compact('data',"sheets"));
    }
    public function getStyles($sd)
    {
        $res = DB::table("colors")->pluck("name","value");
        return response()->json($res);
    }
    public function mcsl_changelog($id = '16')
    {
        $data = DB::table("infos")->where("pub","1")->where("id",$id)->select("id","headline","message","img_big")->orderBy("position","DESC")->first();
        return Inertia::render('Homepage/mfx/infos_show',compact('data'));
    }
    public function home_usershow($nick,$id='')
    {
        $users = DB::table("users")->where("id",$id)->where("pub","1")->where("xis_disabled","0")->select("users.*", "users.xis_aiImage as madewithai")->first();
        \Log::info("HU:".json_encode($users,JSON_PRETTY_PRINT));
        return Inertia::render('Homepage/Usershow', [
            'users' => $users, // statt 'data'
        ]);
    }
    public function home_about()
    {
        return $this->home_usershow("Asario","1");
    }
    //
    public function AddUserAI($val = 0)
    {
        DB::table("users")->where("id",Auth::id())->update(["xis_aiImage"=>$val]);
    }




}
