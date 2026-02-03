<?php

use Inertia\Inertia;


use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CustomLoginController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\MCSLPointsController;
use App\Http\Controllers\PMController;
use App\Http\Controllers\UserConfigController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SQLUpdateController;
use App\Http\Controllers\DarkModeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\NameBindingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HandbookController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardCentralController;
use App\Http\Controllers\DashboardCustomerController;
use App\Http\Controllers\DashboardEmployeeController;
use App\Http\Controllers\ImageUploadController;
use League\CommonMark\CommonMarkConverter;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

    use App\Services\SimpleMailService;

use App\Models\User;
use App\Http\Middleware\CheckSubdomain;
use App\Http\Controllers\TablesController;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RightsController;
use App\Helpers\Settings;

use Illuminate\Http\Request;
use App\Mail\CommentMail;
use App\Mail\ContactMail;
use App\Mail\RegisterMail;
use App\Http\Middleware\HandleSocialitePlusProviders;
use App\Http\Controllers\CountPixelController;
use App\Http\Controllers\Auth\GoogleController;

GlobalController::SetDomain();
//  GlobalController::Redirect();


// dd(class_exists(\App\Http\Middleware\CheckSubd::class)); // Sollte "true" zurückgeben

// if(SD() == "mfx"){
//     Route::get('/', function () {
//         return redirect('/news');
//     });
//     }


// Route::middleware(['checksubd:ab,asario'])->group(function () {
    // Route::middleware('checksubd:ab,asario')->group(function () {
        Route::get('/countpixel', [CountPixelController::class, 'track'])->name('countpixel');
        Route::get("/api/mcslpoints/{users_id?}",[MCSLPointsController::class,"GetCount"])->name("api.mcslpoints");
        Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
        // Route::get('auth/google', function () {
        //     return Socialite::driver('google')->redirect();
        // });

        // Route::get('auth/google/callback', function () {
        //     $googleUser = Socialite::driver('google')->user();

        //     // Debug Output:
        //     // dd($googleUser);
        // });


        //
        // GOOGLE AUTH
        //
        Route::get('/api/delTempz/{path}', [TablesController::class, 'RemoveTempFiles'])->name('remove.temp');
        Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name("ggle.login");
        Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
        Route::get('/register/nickname', [GoogleController::class, 'showNicknameForm']);
        Route::post('/register/nickname', [GoogleController::class, 'saveNickname']);
        Route::get('/auth/nickname', function () {
            return inertia('Admin/nickname');
        })->name('auth.nickname');
        Route::post('/login-silent', [CustomLoginController::class, 'loginSilent'])
        ->middleware('web');
//         dd(
//     Auth::getDefaultDriver(),
//     Auth::guard()->check(),
//     session()->isStarted()
// );



        Route::get("/admin/mcslpoints/{users_id?}",[MCSLPointsController::class,"admin_index"])->name("admin.mcslpoints");
        Route::get("/api/user/rights",[RightsController::class,"GetRights_all"])->name("GetRights_all");
        Route::post('/contact/send', [CommentController::class, 'sendmc']);
        Route::get('/api/pageviews', [CountPixelController::class, 'stats']);
    Route::get('/home/impressum', [HomeController::class, 'imprint_gen'])->name('home.imprint.gen');
        // Route::get('/allroutes', function () {
        //     $routes = collect(Route::getRoutes())
        //         ->filter(fn($route) => in_array('GET', $route->methods()) && !str_contains($route->uri(), '{'))
        //         ->map(fn($route) => url($route->uri()));

        //     return response()->view('allroutes', ['routes' => $routes]);
        // });
        Route::get('/impressum', function ( ){
            return redirect("home/impressum");
        });
//
// DAGIE
//
Route::middleware(\App\Http\Middleware\CheckSubd::class . ':dag,monikadargies')->group(function () {
    Route::get("/", [HomeController::class, "home_index"])->name("home.index");
    Route::get("/api/spruch-des-monats", [HomeController::class, "spruch_des_monats"])->name("home.spruch");
    Route::get("/lostnfound", [HomeController::class, "lostnfound"])->name("home.lostnfound");
    Route::get('/privacy', [HomeController::class, 'home_privacy'])->name('home.privacy.dag');
    Route::get('/ai', [HomeController::class, 'home_AI'])->name('home.ai.dag');
    Route::get("/contacts", [HomeController::class, "dag_contacts"])->name("home.contacts.dag");
    Route::get("/links", [HomeController::class, "dag_links"])->name("home.links");

});

//
// PHOTOS
//
Route::middleware(['auth'])->group(function () {
    Route::put('/user/profile', [ImageUploadController::class, 'update'])->name('user-profile-information.update_alt');
    Route::post('/save/shortpoems', [CommentController::class, 'save_shp'])->name('save.shp');

    Route::get('/profile/photo', [ImageUploadController::class, 'getProfilePhoto'])->name('profile.photo.get');
});
Route::post("/newsl_subscribe", [MailController::class, "Subscribe_Newsl"])->name("mail.subscribe_newsl");
Route::get("/unsubscribe/{uhash}/{email}", [MailController::class, "UnSubscribe_Newsl"])->name("mail.unsubscribe_newsl");
Route::get('/rss.xml', [RssController::class, 'feed']);
Route::get("/mail/subscribe/{uhash}/{email}",[TablesController::class, "newsletter_save"])->name("mail.savenewsletter");
Route::get('/home/ai', [HomeController::class, 'home_AI'])->name('home.ai');
//
//     AB- Asarios BLog
//
Route::middleware(\App\Http\Middleware\CheckSubd::class . ':ab,asario')->group(function () {
    Route::get("/       ",function(){
        return "ASD";
    });
    Route::get('/about/mcs-points', [HomeController::class, 'mcspoints'])->name('mcs.points');
    Route::get('/', [HomeController::class, 'home_index'])->name('home.index');
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
    Route::get('/ri', [HomeController::class, 'home_rindex'])->name('home.rindex');

    Route::get('/newslToMCSLPoints/{uhash?}/{comphash?}/{email?}', [TablesController::class, 'SetNewsl_alt'])->name('newsl.to.mcsp');
    Route::post("/personal_update", [PersonalController::class, 'update'])->name("personal.update");
    Route::get('/home/QRCodaH', [HomeController::class, 'QRCodaH'])->name('home.qrcodah');
    Route::get('/home/aboutme', [HomeController::class, 'home_about'])->name('home.about');
    Route::get("/admin/Kontakte", [TablesController::class, "show_contacts"])->name('admin.kontakte');
    // Imprint
    Route::get('/home/imprint', [HomeController::class, 'home_imprint'])->name('home.imprint');
    // Privacy
    // Terms
    Route::get('/home/terms', [HomeController::class, 'home_terms'])->name('home.terms');
    // Ai Content


    // Pictures
    Route::get('/home/show/pictures/{slug}', [HomeController::class, 'home_images'])->name('home.images.gallery');
    Route::get('/home/search/pictures/{slug}', [HomeController::class, 'home_images_search'])->name('home.images.gallery.search');
    Route::get('/home/search_cat/pictures/', [HomeController::class, 'home_images_search_cat'])->name('home.images.search.cat');
    Route::get('/home/pictures', [HomeController::class, 'home_images_index'])->name('home.images.index');

    // Shortpoems
    Route::get('/home/shortpoems', [HomeController::class, 'home_shortpoems'])->name('home.shortpoems');
    // DidYouKnow
    Route::get('/home/didyouknow', [HomeController::class, 'home_didyouknow'])->name('home.didyouknow');

    // Blogs
    Route::get('/blogs', [HomeController::class, 'home_blog_index'])->name('home.blog.index')->middleware('remember');
    Route::get('/blogs/show/{autoslug}', [HomeController::class, 'home_blog_show'])->name('home.blog.show');

    // Root-Redirect
    Route::get('/', function () {
        return redirect()->route('home.index');
    })->name('home.start');
    // Dashboard redirect

    // Fehlerseiten


    Route::get('/home/no_application_found', [HomeController::class, 'home_no_application_found'])->name('home.no_application_found');
    Route::get('/home/user_is_no_admin', [HomeController::class, 'home_user_is_no_admin'])->name('home.user_is_no_admin');
    Route::get('/home/user_is_no_employee', [HomeController::class, 'home_user_is_no_employee'])->name('home.user_is_no_employee');
    Route::get('/home/user_is_no_customer', [HomeController::class, 'home_user_is_no_customer'])->name('home.user_is_no_customer');
    Route::get('/home/invalid_signature', [HomeController::class, 'home_invalid_signature'])->name('home.invalid_signature');

    // Users
    Route::get('/home/users', [HomeController::class, 'home_userlist'])->name('home.userlist');
    Route::get('/home/users/show/{name}/{id?}', [HomeController::class, 'home_usershow'])->name('home.user.show');

    // Pictures PagesController
    // Route::get('/pictures', [App\Http\Controllers\PagesController::class, 'ab_images'])->name('images');
    // Route::get('/pictures/{pic}', [App\Http\Controllers\PagesController::class, 'ab_images_cat'])->name('pictures');

}); // <-- schließt Middleware group
Route::get('/home/no_application_found', [HomeController::class, 'home_no_application_found'])->name('home.no_application_found');
Route::get('/home/user_is_no_admin', [HomeController::class, 'home_user_is_no_admin'])->name('home.user_is_no_admin');
Route::get('/home/user_is_no_employee', [HomeController::class, 'home_user_is_no_employee'])->name('home.user_is_no_employee');
Route::get('/home/user_is_no_customer', [HomeController::class, 'home_user_is_no_customer'])->name('home.user_is_no_customer');
Route::get('/home/invalid_signature', [HomeController::class, 'home_invalid_signature'])->name('home.invalid_signature');
Route::get('/home/privacy', [HomeController::class, 'home_privacy'])->name('home.privacy');

    #
    #
    # MFX MarbleFX
    #
    #
    Route::middleware(\App\Http\Middleware\CheckSubd::class . ':mfx,marblefx')->group(function () {
        Route::get('/', [HomeController::class, 'home_index'])->name('home.index');
        Route::get('/ri', [HomeController::class, 'home_rindex'])->name('home.rindex');

        Route::get('/changelog', [HomeController::class, 'mcsl_changelog'])->name('mfx.changelog');
        Route::get('/changelog_old', [HomeController::class, 'changelog_old'])->name('mfx.changelog.old');

        Route::get('/home/projects', [HomeController::class, 'projects'])->name('home.projects.mfx');

        Route::get('/home/images', [HomeController::class, 'home_images_cat_mfx'])->name('home.images.cat.mfx');
        Route::get('/home/images/show/{id}', [HomeController::class, 'home_images_show_mfx'])->name('home.images.mfx');

        Route::get('/home/people', [HomeController::class, 'people'])->name('home.people.mfx');

        Route::get('/home/privacy_info', [HomeController::class, 'infos_privacy'])->name('home.privacy.mfx');

        Route::get('/home/infos', [HomeController::class, 'infos_index'])->name('home.infos.mfx');

        Route::get('/home/infos/show/{id}', [HomeController::class, 'infos_show'])->name('home.infos.show.mfx');
		// Route::get('/home/privacy', [HomeController::class, 'infos_privacy'])->name('home.privacy');
        Route::get('/powered-by-mcsl', [HomeController::class, 'infos_pow'])->name('home.powered.show.mfx');
        Route::get('/dashboard', function () {
            return redirect('/admin/dashboard');
        })->name('dashboard');
        Route::get('/contacts_mfx', [HomeController::class, 'contacts_mfx'])->name('home.contacts.mfx');
    });

    Route::get('/api/table-columns/{table}', [TablesController::class, 'getTableColumns'])
    ->name('api.table-columns');

    // OLD LOGIN

    //Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');


    Route::get('/home/contacts', [HomeController::class, 'contacts'])->name('home.contacts');
    // Login absenden
    // Route::post('/login', [CustomLoginController::class, 'login']);
    Route::get('register', [RegisteredUserController::class, 'create'])
    // ->middleware(HandleSocialitePlusProviders::class)
    ->name('register');

// Route::get('login', [AuthenticatedSessionController::class, 'create'])
//     // ->middleware(HandleSocialitePlusProviders::class)
//     ->name('login');
    Route::post('/logout', [CustomLoginController::class, 'logout'])
    ->name('logout');
//     Route::get('/login', function () {
//     return Inertia::render('Auth/Login');
// })->middleware('guest')->name('login');
//Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');

// Login POST
Route::post('/login', [CustomLoginController::class, 'login'])
    ->middleware('guest');

    // 2FA-Challenge
    Route::get('/two-factor-challenge', [CustomLoginController::class, 'showTwoFactorForm'])
    ->name('two-factor.login');
    Route::post('/user/twofactor/enable', [CustomLoginController::class, 'enableTwoFactor'])
    ->name('user.twofactor.enable');
    Route::post('/user/twofactor/disable', [CustomLoginController::class, 'disableTwoFactor'])
    ->name('user.twofactor.disable');
    Route::post('/two-factor-challenge', [CustomLoginController::class, 'twoFactorLogin'])
        ->name('two-factor.login.post');
    // Route::get('/two-factor-challenge', [CustomLoginController::class, 'showTwoFactorForm'])
    //     ->name('two-factor.login');
    // Route::post('/two-factor-challenge', [CustomLoginController::class, 'twoFactorLogin']);
    // Route::get('/login', [CustomLoginController::class, 'create'])->name('login');
    // Route::post('/login', [CustomLoginController::class, 'store']);

    // // Fortify-Routen manuell neu laden (außer login)
    // \Laravel\Fortify\Fortify::routes(function ($router) {
    //     $router->register();
    //     $router->resetPassword();
    //     $router->emailVerification();
    // });


// include __DIR__."/extraroutes.php";
Route::get('/test-admin', function () {
if(Auth::check()){
    return '✅ Middleware is_admin funktioniert';
}

});
Route::get('/copyleft/images', [ImageUploadController::class, 'CopyLeft'])->name('images.copyleft');
Route::get('/db-check', function () {
    $tenant = App\Models\Tenant::first(); // oder aus Subdomain ermitteln
    $connection = $tenant->getConnectionName(); // "mariadb_mfx"

    // Optional DB Name auslesen
    $dbName = $tenant->getConnection()->getDatabaseName();

    return response()->json([
        'connection' => $connection,
        'db' => $dbName,
        'host' => request()->getHost(),
    ]);
});
// Route::post("/cookie_acceptall",[AcceptAllController::class,'__invoke'])->name("cookieconsent.accept.all2");
// Route::post("/cookie_acceptess",[AcceptEssentialsController::class,'__invoke'])->name("cookieconsent.accept.essentials2");
// Route::post("/cookie_config",[ConfigureController::class,'__invoke'])->name("cookieconsent.accept.configuration2");

// Route::domain('{domain}')
//     ->where('domain', '(mfx\.localhost\.de|www\.marblefx\.de)')
//     ->group(function () {
   $sub = SD();
    // dd($sub);
    // Route::middleware([])->group(function () {
    //     $sub = $sub ?? SD();


    //     // foreach ($domains as $key => $host) {
    //     //     Route::domain($host)
    //     //         ->as($key . '.')            // => z.B. mfx.home  / marble.home
    //     //         ->group(function () use ($key) {

        // Route::middleware(\App\Http\Middleware\CheckSubd::class . ':mfx')->group(function () {
        //     Route::get("/", [HomeController::class, "home_index"])->name("home.index");
        // });












            // Route::post('/api/save-json', function (Request $request) {
            //     $path = public_path($request->input('path'));
            //     $data = $request->input('data');

            //     // if (!str_starts_with(realpath($path), public_path('images'))) {
            //     //     return response()->json(['error' => 'Ungültiger Pfad'], 403);
            //     // }

            //     File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            //     return response()->json(['success' => true]);
            // });




Route::get('/api/getVotez', [HomeController::class,"getVotez"])->name("mfx.getvotez");
// MAILFORM SUBMIT

// Route::post('/mail-test',[CommentController::class,"sendm"]);

Route::get('/mail-test', function () {
    // $nick = "Animal";
    // $content = "test@example.com";

    // Mail::to('parie@gmx.de')->send(
    //     new RegisterMail(
    //         '[MCSL] - Neuer Nutzer auf '.request()->getHost(),
    //         'http://' . request()->getHost() . '/admin/tables/users/show?search=' . $nick,
    //         $nick,
    //         $content
    //     )
    // );

    // return 'Mail wurde versendet (oder an Transport übergeben).';

});

// Route::post('/register', [RegisteredUserController::class, 'store'])
//      ->name('register.override');



// Route::get('/mail-test', function () {
    // Mail::to('parie@gmx.de')->send(new CommentMail('Asario.de', 'http://localhost:8081/admin/tables/comments/show',auth()->user()->name,$comment->content));
    // return 'Mail gesendet!';
// });

Route::get("/api/user/rights/des/{table}/{right}",[RightsController::class,"GetRights"])->name("GetRights");

Route::get("/api/user/rights/des-all/{right}",[RightsController::class,"allTableRights"])->name("allRights");




Route::get('/no-rights', [HomeController::class, 'no_rights'])->name('tables.noview');


Route::get('/api/settings', [SettingsController::class, 'all']);
Route::get('/api/GetLastAct', function (){

    return response($_SERVER['HTTP_REFERER']);
});
Route::get('/GetAuth', function () {
    if (Auth::check()) {
        // \Log::info("✅ Eingeloggt, User-ID: " . Auth::id());
        return response()->json("true");
    }

    // \Log::i  nfo("🚨 Nicht eingeloggt");
    return response()->json("false");
})->name("GetAuth");
 Route::get("/GETUserID", function (){
     return response()->json(Auth::id());
});
Route::get('/get-total-rating/{table}', [RatingController::class, 'getTotalRating']);
Route::get('/api/GetRating/{table}/{postId}', [RatingController::class, 'getRat'])->name("api.getRating");

Route::get("/admin/user-rights/get",[TablesController::class,'GetURights'])->name("admin.users_rights.get");
Route::middleware(['auth'])->group(function () {
    Route::post('/two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm_alt');
    Route::get("/admin/User_Rights",[TablesController::class,'URights'])->name("admin.users_rights");
    Route::get("/api/GetSRights/",[TablesController::class,'GetSRights'])->name("admin.GetSRights");
    Route::post("/api/admin/user-rights/save",[TablesController::class,'SaveURights'])->name("admin.users_rights.save");



    Route::get("api/getSlug/{table}/{id?}", [TablesController::class, "getSlug"])->name("getSlug");
});

Route::get('/namebindings', [NameBindingsController::class, 'RefreshFields'])->name("ColumnFetcher");
Route::post('/upload-image/{table}/{isw?}', [ImageUploadController::class, 'upload'])->name('upload.image');
Route::post('/upload-image_alt/{table}/{isw?}/{oripath?}', [ImageUploadController::class, 'upload_ori'])->name('upload.gen');
Route::get('/GetUserNull', [TablesController::class, 'GetUserNull'])->name('GetUserNull');

Route::post('/save-image/{table}', [ImageUploadController::class, 'save'])->name('save.image');
Route::get('/comments/{table}/{postId}', [CommentController::class, 'fetchComments'])->name('comments.fetch');
Route::post('/save-rating', [RatingController::class, 'saveRating']);
Route::middleware(['auth'])->group(function () {

});
Route::get('/get-rating/{table}/{postId}', [RatingController::class, 'getStars']);
Route::get('/get-average-rating/{table}/{postId}', [RatingController::class, 'getAverageRating']);
Route::get('/tables/form-data/{table}/{id}', [TablesController::class, 'ExportFields'])
->name("GetTableForm");
Route::get('/pb', [RatingController::class, 'pb'])
->name("pb");
// ========
// Homepage
// ========
// Startseite

Route::get('/devmod', function () {
    // ShowRepo();
    //IMULController::smpix();
    //  DevMod();
    // ConvertTypes();
    return date("Y-m-d H:i:s",'1501344360');
    //  small_images(public_path()."/images/_ab/users");
     // Hauptprogramm
    // $viewsPath = resource_path('views/layouts'); // Pfad zu den Blade-Dateien
    // $publicJsPath = public_path('js');          // Zielordner für JavaScript-Dateien
    // $publicCssPath = public_path('css');        // Zielordner für CSS-Dateien
    // updateBladeFiles($viewsPath, $publicJsPath, $publicCssPath);


    })->name('devmod');
    Route::get('/get-nick-from-comments', function (Request $request) {
        return DB::table('comments')
            ->where('comments.id', $request->id)
            ->select('comments.nick')
            ->first(); // nur ein Eintrag
    })->name('getnickfromcomments');
    // Route::post('/toggle-darkmode', [App\Http\Controllers\DarkModeController::class, 'toggle'])->name('toggle.darkmode');
    Route::post('/toggle-pub', [TablesController::class, 'togglePub'])->name('toggle.pub');
    Route::get('tables/{table}/create', [TablesController::class, 'createEntryForm'])->name('tables.create-table');
    // Route::post('/comments/store/{table}/{postId}', [CommentController::class, 'store_alt'])->name('comments.store_alt');



    Route::post('/comments/store/{table}/{id}', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/api/admin_table_positions', [RightsController::class,"GetTables_posi"])->name("GetTablesPosi");
    // Route::get('/{table}/{cat?}#headline_{id}', [PostController::class, 'show'])->name('posts.show');

//     Route::get(
//         '/admin/dashboard',
//         [DashboardAdminController::class, 'admin_index']
//     )->name('dashboard');
// ===============================
// Routen für angemeldete Anwender
// ===============================

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // =================
    // APPLICATIONSWITCH
    // =================
    Route::get('/applicationswitch', [ApplicationController::class, 'index'])->name('applicationswitch');


### ============ Authed API qa ============== ###

    //Adde 1/0 bei profil für AI Made ProfilImage
    Route::post('/api/AddUserAI/{val}', [HomeController::class, 'AddUserAI'])->name("AddUserAI");
    // Add comment entry to DB (only logged in users)







    // =================
    // CENTRAL DASHBOARD
    // =================
    // Central-Dashboard
    // $subdomain = GlobalController::SD();
    // if ($subdomain != "ab" && $subdomain != "localhost"){
    //     $dda = "_" . $subdomain;
    // } else {
    //     $dda = '';
    // }

    // $controller = "App\\Http\\Controllers\\HomeController{$dda}";

    // Route::get('/news', [$controller, 'home_index'])->name('dashboard_alt');

    // =================
    // APPLICATION ADMIN
    // =================
    Route::middleware(['is_admin'])->group(function () {

        Route::get('/dashboard', function () {
            return redirect('/admin/dashboard');
        })->name('dashboard');
        // Dashboard
        Route::get(
            '/admin/dashboard',
            [DashboardAdminController::class, 'admin_index']
        )->name('admin.dashboard');

### ============== API ISADMIN QI ================ ###

        Route::get("/admin/get_unused_imgz/{dom?}", [TablesController::class,"getunused"])->name("admin.getunused");
        Route::post('/admin/zip-images/{dom?}', [TablesController::class, 'zipImages'])->name("zip.images");
        Route::post('/admin/removeFiles', [TablesController::class, 'remImages'])->name("rem.images");
        Route::get("/admin/SQLUpdate", [SQLUpdateController::class,"index"])->name("SQL.index");
        Route::get('/api/admin-tables', [TablesController::class, 'GetDBTables'])->name("get.db.tables");
        Route::get('/userx/update-config/{id}', [UserConfigController::class, 'updateConfig'])->name('usconfi');
        Route::get('/dboard/data', [CountPixelController::class, 'dboard'])->name('dboard.data');
        Route::get('/admin/Statistics', [HomeController::class, 'plot_gfx'])->name('stats');
        // Route::post('/api/AddFunc', [RightsController::class, 'AddFunction'])->name('admin.add.func');
        Route::get('/get_MCSL_Points_Preniums', [MCSLPointsController::class, 'SelectPremiums'])->name('store.mcslpoints');
        Route::get('/SubmitPremiums/{users_id}/{img_id}', [MCSLPointsController::class, 'SendMail'])->name('send.mcslpoints');
        //
        Route::post('/api/user/batch-rights', [TablesController::class, 'GetBatchRights'])->name("get.bash.rights");
        Route::get('/api/chkcom/{id?}', [CommentController::class, 'checkComment'])->name("comments.check");
        Route::get('/api/contacts', [TablesController::class, 'api_contacts'])->name("admin.contacts");
        Route::post("/personal_update", [PersonalController::class, 'update'])->name("personal.update");
        Route::get('/api/created-at', [TablesController::class, 'getCreatedAt'])->name("created.at");
        Route::get("/api/GetCat/{table}/{id}", [TablesController::class, 'GetCats'])->name("GetCats");
        Route::post("/api/save-order/{table}", [TablesController::class, 'save_order'])->name("save-order");
        Route::get("/pm/index", [PMController::class, 'pm_index'])->name("pm.index");
        Route::post("/pm/save", [PMController::class, 'store'])->name("pm.save");
        Route::post("/admin/pm/check/{id}",[PMController::class,"update"])
            ->name("admin.pm.check");
        Route::post('/admin/pm/mark', [PmController::class, 'update_more'])->name('admin.pm.mark');
        Route::delete("/admin/pm/delete/{table}/{id}",[PMController::class,"destroy"])
            ->name("admin.pm.delete");
        Route::post("/admin/pm/delmore/",[PMController::class,"destroy_more"])
            ->name("admin.pm.delmore");
        // Route::get('/email/signatur/{id}', function ($id) {
        //     return \App\Models\Signatur::select('id', 'name', 'sigtext')->findOrFail($id);
        // });
        // Route::get("/api/created-at", [TablesController::class],'GetCreatedAt')->name("created.at");
        // Route::get("api/get-image-id/{table}/{id}",[TablesController::class,"GetImageId"])
        //     ->name("api-get-image-id");

        Route::get('/api/user-id', function () {
            $user = Auth::user();
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
            ]);
        });

        Route::put("/admin/UsConf/save/",[TablesController::class,"us_config_save"])
            ->name("admin.usconf.save");

        Route::get('/api/GetUsConf', function () {
        $form = DB::table("users_config")->where("users_id",Auth::id())->get();
            return response()->json([
                'form' => $form,

            ]);
        });

        Route::get('/api/roles/{urid}', [TablesController::class, 'getRoles']);
        Route::get("/api/images/{table}/{id}",[TablesController::class,"GetImageUrl"])
            ->name("/api-get-image-url");
        Route::get('/api/headlines/{table}', [TablesController::class, 'getHeadlines']);
        Route::post('/api/entries/update-position/{table}', [TablesController::class, 'updatePosition']);
        Route::delete('/comments/delete/{comment_id}', [CommentController::class,'destroy_comments'])->name("destroy.comments");
        Route::post('/api/save-json', [ImageUploadController::class, 'store_json'])->name("save-json-gallery");
        Route::post('api/saveFolder',  [ImageUploadController::class, 'store_dir'])->name("save-dirsave");
        Route::post("/api/del_image/{column}/{folder}/{posi}", [ImageUploadController::class, 'Del_Image'])->name("remove.img");
        Route::get('/api/tailwind-colors/{subdomain}', [HomeController::class,"getStyles"])->name("mfx.getstyles");
        Route::post('/api/getCheckedBatch', [CommentController::class, 'CheckCommentsDone'])->name("comments.check.done");


        // =================
        // Laravel-Log-Datei
        // =================
        // Display Log-Datei
        Route::middleware(['auth', 'right:LogViewer'])->group(function () {
            Route::get(
                '/admin/laravel_log',
                [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']
            )->name('admin.laravel_log');
        });
        // ========
        // Handbook
        // ========
        Route::get('/admin/handbook', [HandbookController::class, 'admin_handbook'])->name('admin.handbook');
        // =====
        // Blogs
        // =====
        // Liste der Blogartikel (blogs)
        Route::get('/admin/blogs/index', [BlogController::class, 'admin_blog_index'])
            ->name('admin.blog.index')->middleware('remember');
        // Create a new Blogartikel
        Route::get('/admin/blogs/create', [BlogController::class, 'admin_blog_create'])
            ->name('admin.blog.create');
        // Store Blogartikel
        Route::post('/admin/blogs/store', [BlogController::class, 'admin_blog_store'])
            ->name('admin.blog.store');
        // Edit des Blogartikels
        Route::get('/admin/blogs/{blog}/edit', [BlogController::class, 'admin_blog_edit'])
            ->name('admin.blog.edit');
        // Update des Blogartikels
        Route::put('/admin/blogs/{blog}', [BlogController::class, 'admin_blog_update'])
            ->name('admin.blog.update');

        Route::get('/api/getuname', [TablesController::class, 'getUserName']);
        Route::get('/api/users_rights', [TablesController::class, 'getUserDevs']);
        Route::post('/api/save-user-roles', [TablesController::class, 'store_user_rights']);
        Route::post('//api/save-user-disabled', [TablesController::class, 'saveUserDisabled']);


        // ====
        // Tables
        // ====
        // Store Table Entry
        Route::post('/admin/tables/store/{table}', [TablesController::class, 'StoreTable'])
            ->name('admin.tables.store');
        // Delete table
        // Route::post('/admin/tables/{table}/delete', [TablesController::class, 'DeleteTable'])
        //     ->name('admin.tables.delete');
        // Tables Create
        Route::get('/admin/tables/copy/{table}/{id}', [TablesController::class, 'CopyTable'])
            ->name('admin.tables.copy');
        Route::get('/admin/tables/create/{table}', [TablesController::class, 'CreateTable'])
            ->name('admin.tables.create');
        // Tables Index
        Route::get('/admin/tables/', [TablesController::class, 'ListTables'])
            ->name('admin.tables.index');
        // Tables Show
        Route::get("admin/tables/{table}/show",[TablesController::class,"ShowTable"])
            ->name("admin.tables.show");
        Route::get('/tables/sort-data/{name}', [TablesController::class, 'getOptionz'])
            ->name("GetTableOpt");
        Route::get('/tables/pwform', [GlobalController::class, 'PasswordForm'])
            ->name("pw.form");
        Route::post('/tables/pwprint', [GlobalController::class, 'PasswordPrint'])
            ->name("pw.print");
        // Tables Edit table
        Route::get("/admin/tables/edit/{id}/{table}",[TablesController::class,"EditTables"])
            ->name("admin.tables.edit");
        // Tables Delete
        Route::delete("/admin/tables/delete/{table}/{id}",[TablesController::class,"DeleteTables"])
        ->name("admin.tables.delete");
        // Tables UPDATE
        Route::post("/admin/tables/update/{table}/{id?}",[TablesController::class,"UpdateTable"])
            ->name("admin.table.update");
        // Email Modul
        Route::get('/admin/email', [TablesController::class, 'emailmod'])->name('admin.mailcenter');

        Route::post('/email/preview', [TablesController::class, 'prev_newsl'])->name('admin.mailprev');
        Route::post('/email/save', [MailController::class, 'saveMail'])->name('admin.mail.save');
        Route::post('/email/signatur/save', [MailController::class, 'saveSignature'])->name('admin.email.signatur.save');

        Route::get('/email/send/', [MailController::class, 'send_newsletter'])->name('admin.mail.send');
        // Blogartikel Delete
        Route::delete('/admin/blogs/{blog}', [BlogController::class, 'admin_blog_delete'])
            ->name('admin.blog.delete');


        Route::get('/admin', function () {
            return redirect()->route('admin.dashboard');
        })->name('admin.redirect.route');

        Route::get('/hasCreated/{table}',[RightsController::class,"HasCreated"])->name('hasCreated');


        // =======
        // Profile
        // =======
        Route::get('/admin/profile', [DashboardAdminController::class, 'admin_profile'])
            ->name('admin.profile');
        // ==========
        // Api-Tokens
        // ==========
        Route::get('/admin/api_tokens', [DashboardAdminController::class, 'admin_api_tokens_index'])
            ->name('admin.api_tokens.index');


        });


        // Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        //Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout_sfa');
    // ====================
    // APPLICATION EMPLOYEE
    // ====================
    Route::middleware(['is_employee'])->group(function () {
        // Dashboard
        Route::get('/employee/dashboard', [DashboardEmployeeController::class, 'employee_index'])
            ->name('employee.dashboard');
    });

    // ====================
    // APPLICATION CUSTOMER
    // ====================
    Route::middleware(['is_customer'])->group(function () {
        // Dashboard
        Route::get('/home/dashboard', [DashboardCustomerController::class, 'customer_index'])
            ->name('customer.dashboard');

        });

});
// Comments



// Darkmode Route

;
Route::get('/api/dark-mode', function () {

    return response()->json(['darkMode' => session('dark_mode', 'dark')]);
});
//
// ==============
// Fallback-Route
// ==============
Route::get('/tables/sort-enum/{table}/{name}', [TablesController::class, 'getOptionz_sel'])
        ->name("GetTableEnum");
Route::get('/act-category/{table}/{id?}', [CategoryController::class, 'index'])
    ->name("ArtAct");
    // Route::post('/toggle-dark-mode', [DarkModeController::class, 'toggle'])->name('toggle-dark-mode');
    Route::post('/toggle-dark-mode', [DarkModeController::class, 'toggle'])->name('toggle.darkmode');
Route::get('/tables/sort-enumis/{table}/{name}', [TablesController::class, 'getOptionz_itemscope'])
        ->name("GetTableItemScope");


             $sub = SD();
        //     //



        // Route::post('reset-password', [CustomLoginController::class, 'pw_recovery'])
        //     ->name('password.store');







        // elseif($subdomain == "mfx")
        // {
        //     // dd($subdomain."asd");

        //
        // }
        // elseif($subdomain != "localhost"){
        //     $hc = "App\\Http\\Controllers\\HomeController_" . $subdomain;
        //     Route::get('/', [$hc, 'home'])->name('home.index');
        //     Route::get('/', [$hc, 'home'])->name('home.'.$subdomain);
        // }
        // else{
        //     $hc = "App\\Http\\Controllers\\HomeController";
        //     Route::get('/', [$hc,'home_index_alt'])->name('home.index');
        //     Route::get('/', [$hc,'home_index_alt'])->name('home.mfx');
        // }
        // include __DIR__."/auth.php";
        // Auth::routes();
        Route::get("/",[HomeController::class,"home_index"])->name("home.index");
        Route::get("/news", function() {
            return redirect("/");
        });

        Route::get('/ri', [HomeController::class, 'home_rindex'])->name('home.rindex');
        Route::fallback(function () {
            CountPixelController::o404();
            return Inertia::render('Homepage/NoPageFound_'.SD());
        });



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
