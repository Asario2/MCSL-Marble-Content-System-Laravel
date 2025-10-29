<?php
// Beispielcontroller
namespace App\Http\Controllers;

use App\Models\Post; // Importiere das Model
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\IMULController;
use App\Http\Controllers\GlobalController;
use App\Models\BlogPost;
use App\Models\Neues;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageServiceProvider;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\Schema;
class PagesController extends Controller
{
    public function __construct()
    {
        $this->time = time();
        $this->globalController = NEW GlobalController();
        $this->globalController->SetDomain();


    }
    public function dl_ab()
    {

        $data = DB::table('devlog')
                      ->join('users', 'devlog.author_id', '=', 'users.id')
                      ->select('devlog.*', 'users.name as author_name')
                      ->orderBY("id","DESC")->get();
        return view("ab.devlog",compact('data'));
    }
    public function mb_ab()
    {
        $data = DB::table('mindblog')
                      ->join('users', 'mindblog.author_id', '=', 'users.id')
                      ->select('mindblog.*', 'users.name as author_name')
                      ->orderBY("created_at","DESC")->get();
        return view("ab.mindblog",compact('data'));
    }
    public function ai_ab()
    {
        $data = DB::table("texts")->where("autoslug","ai")->get();
        return view("ab.ai",compact('data'));
    }
    public function ab_sp()
    {
        $data = DB::table("shortpoems")->where("pub","1")->orderBY("date","DESC")->get();
        return view("ab.shpo",compact('data'));

    }
    public function im_loadMore(Request $request,$tid='')
    {
        $offset = $request->input('offset', 0);
        $limit = 10;

        // Weitere Beiträge laden
        $posts = DB::table('images')
                    ->select('images.*')
                    ->joinSub(
                        DB::table('images')
                                ->select('id', DB::raw('MAX(created_at) as latest_created_at'))
                                ->where('image_categories_id', $tid)
                                ->where('pub', 1)
                                ->groupBy('id'),
                            'subquery',
                            'images.id',
                            '=',
                            'subquery.id'
                        )
                        ->orderBy('subquery.latest_created_at', 'desc')
                        ->skip($offset)
                        ->take($limit)
                        ->get();
        $data = $posts;
        $comments = []; // Initialisiere das Array
        $postIds = DB::table('comments')->pluck('post_id'); // Pluck gibt ein Array mit den IDs zurück
        foreach ($postIds as $id) {

        $comments[$id] = DB::table('comments')
            ->join('users', 'comments.users_id', '=', 'users.id')
            ->where('comments.post_id', '=', $id)
            ->orderBy("comments.id", "ASC")
            ->select('users.name',"users.image_path", 'comments.*')
            ->get();
            // Filtert eindeutige Einträge basierend auf der Spalte 'comments.id'


    $ratings[$id] = DB::table('ratings')
    ->where('image_id', $id) // Ersetze $id mit der gewünschten image_id
    ->selectRaw('COALESCE(AVG(rating), 0) as a_rating')
    ->pluck('a_rating');

}
$table = "images";
        // HTML-Fragment zurückgeben
        return view('posts.partials.images_items', compact('table','posts','data',"comments",'ratings'))->render();

}
    public function ab_images()
    {
        $data = DB::table("image_categories")->orderBy("name","ASC")->where('pub',"1")->get();
        // vde($data);
        return view("ab.images",compact("data"));
    }
    public function ab_images_cat(Request $request,$gallery)
    {

        $limit = $take = 10;
        $offset = $request->get('offset', 0);
        $ord[0] = "images.created_at";
        $ord[1] = "DESC";
        if($gallery == "digital")
        {
            $ord[0] = "images.position";
            $ord[1] = "ASC";
        }
        if($gallery == "alphabet")
        {
            $ord[0] = "headline";
            $ord[1] = "ASC";
        }
        $data = DB::table('images')
            ->join('image_categories', 'images.image_categories_id', '=', 'image_categories.id')
            ->where('image_categories.shortname', '=', $gallery)
            ->where("images.pub","1")
            ->orderBy($ord[0],$ord[1])
            ->select('images.*') // Wählt alle Spalten aus der Tabelle "images"
            // ->take($limit)
            // ->offset($offset)
            ->get();
        $postIds = DB::table('comments')->pluck('post_id'); // Pluck gibt ein Array mit den IDs zurück

        $comments = []; // Initialisiere das Array
        $userRatings = [];
        $ratings = $userrat = [];
        $processedIds = session()->get('comment_ids', []);
        foreach ($postIds as $id) {

        $comments[$id] = DB::table('comments')
            ->join('users', 'comments.users_id', '=', 'users.id')
            ->where('comments.post_id', '=', $id)
            //->whereNotIn('comments.id', $processedIds)
            ->orderBy("comments.created_at","asc")
            ->select('users.name','users.image_path', 'comments.*')
            ->take($take)
            ->get();
$processedIds = session()->get('comment_ids', []);
        $newIds = $comments[$id]->pluck('id')->toArray();
        session()->put('comment_ids', array_merge($processedIds, $newIds));

    $ratings[$id] = DB::table('ratings')
    ->where('image_id', $id) // Ersetze $id mit der gewünschten image_id
    ->selectRaw('COALESCE(AVG(rating), 0) as a_rating')
    ->pluck('a_rating');

        if(!@$ratings[$id])
        {
            $id = 1;
            $ratings = [];
        }



            // Benutzerbewertungen für jeden Post abrufen
           $userRatings[$id] = DB::table('ratings')
                ->where('image_id', $id)
                ->selectRaw('COALESCE(AVG(rating), 0) as a_rating')
                ->value('a_rating');
                $userrat[$id] = DB::table("ratings")->where("users_id",Auth::id())->where('image_id',$id)->first();


            $comments[$id] = Comment::join('users', 'comments.users_id', '=', 'users.id')
            ->where('comments.post_id', '=', $id)
            ->orderBy('comments.created_at', 'desc')
            ->select('users.name','users.image_path', 'comments.*')
            ->take($take)
            ->offset($offset)
            ->get();
        }

        $posts = []; // Post::all(); Hol alle Posts


        // $comments = DB::table('comments')
        //     ->join('users', 'comments.users_id', '=', 'users.id')
        //     ->where('comments.post_id', '=', $id)
        //     ->orderBy("comments.created_at","asc")
        //     ->select('users.*', 'comments.*');


//
            // Gruppiere Kommentare nach Post-ID
        // $comments = Comment::paginate(10); // 10 Posts pro Seite



        return view("ab.images_cat",compact("data",'posts',"comments","userRatings","ratings"));
    }

    public function DYK()
    {
        $data = DB::table("didyouknow")->where("pub","1")->orderBY("position","DESC")->get();
        return view("ab.dyk",compact('data'));
    }
    public function hm_start()
    {
        $data = DB::table("starts")->where("autoslug","start")->get();
        return view("hm.start",compact('data'));
    }
    public function bio_hm()
    {
        $data = DB::table("starts")->where("autoslug","bio")->get();
        return view("hm.bio",compact('data'));
    }
    public function music_hm()
    {
        $data = DB::table("starts")->where("autoslug","music")->get();
        return view("hm.music",compact('data'));
    }
    public function kontakt_hm()
    {
        $data = DB::table("kontaktdaten")->where("dom","hm")->first();
        return view("hm.kontakt",compact('data'));
    }
    function kontakt_send(Request $request)
    {
        $MailHelper = NEW MailHelper();
        $data = DB::table("mails")->where("name",'contact_form')->first();
        $message = nl2br($request->message);
        $to = $data->to_address;
        $subject = $data->subject;
        $MailHelper->SendMailer($to, $subject, $message,'','',$request->email,1);
        return redirect()->route("kontakt.index")->with("success","Mail gesendet");
    }

    //
}
