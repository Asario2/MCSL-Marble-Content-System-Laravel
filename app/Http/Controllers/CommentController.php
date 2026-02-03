<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Comment;
use App\Models\Post;
use Auth;
use App\Mail\CommentMail;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\MailController;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\DB;
use App\Models\Rating;
use App\Models\User;
use App\Models\UsersRight;
use Illuminate\Support\Facades\Schema;

class CommentController extends Controller
{
    public $ofst;
    function __construct()
    {
        $this->processedIds = session()->get('processedIds', []);
        if(!session_id()){
            session_start();
        }
    }


    /**
     * Speichert einen neuen Kommentar.
     */
    public function store_rating(Request $request)
    {
        // Validierung der eingehenden Daten
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'id' => 'required|integer',          // Image ID
            'table' => 'required|string' // Optional: um sicherzustellen, dass es sich um eine Bildbewertung handelt
        ]);

        // Optional: Nutzer ID (z.B. wenn Nutzer angemeldet sind)
        $userId = auth()->id();
        // Bewertung speichern
        if(!$userId)
        {
            return response()->json([
            'status' => 'error',
            'message' => 'Bitte Loggen sie sich ein.'
            ]);
        }
        $rating = DB::table('ratings')
            ->where('table', $validated['table'])
            ->where('users_id', $userId)
            ->where('image_id', $request->id)
            ->update([
                'users_id' => $userId,
                'image_id' => $request->id,
                'table' => $validated['table'],
                'rating' => $validated['rating'],
            ]);



        $alreadyPosted = DB::table("ratings")->where('table',$validated['table'])->where("users_id",$userId)->where('image_id',$request->id)->first();
        if(!$alreadyPosted)
        {
        DB::table("ratings")->insert([
            'users_id' => $userId,
            'image_id' => $request->id,
            'table' => $request->table,
            'rating' => $request->rating,
        ]);
        }
        $upd = ["created_at"=>now()];
        $updu = ["updated_at"=>now()];
        DB::table("ratings")->where("users_id",$userId)->where("image_id",$request->id)->where("table",$request->table)->where("created_at",NULL)->update($upd);
        DB::table("ratings")->where("users_id",$userId)->where("image_id",$request->id)->where("table",$request->table)->update($updu);

        return response()->json([
            'status' => 'success',
            'message' => 'Bewertung erfolgreich gespeichert',
            'rating' => $rating
        ]);
    }

     public function store_alt(Request $request,$table='')
    {
        // Validierung des Eingabewerts
        // $request->validate([
        //     'comment' => 'required|string|max:4000',
        //     'comment2' => 'string|max:4000' // Maximale Länge kann angepasst werden
        // ]);
        $user = DB::table("users")->where("id",Auth()->id())->select("email","name")->first();
        // Kommentar erstellen und in der Datenbank speichern
        // \Log::info("USAH".json_encode($user));
        // if (!auth()->check()) {
        //     return response()->json(['redirect' => route('login')]);
        // }
        $pgo = $table;
        if($table == "pictures")
        {
            $table = "images";
        }
        if(substr_count($table,"users"))
        {
            $table = "users";
        }
        if(substr_count($table,"blogs"))
        {
            $table = "blogs";
        }
        $table_alt = $table;

            $now = now();
        $adtabid = DB::table("admin_table")->where("name",$table)->pluck("id")->first();
        $comment = new Comment();
        $comment->content = $request->input('comment2') ?? $request->comment;
        $comment->content = strip_tags($comment->content, '<br>');
        $comment->admin_table_id = $this->GetTid($table_alt);
        $comment->users_id = auth()->id() ?? "7"; // Beispiel für Benutzer-ID

        $comment->created_at = $now;
        $comment->updated_at = $now;
        $comment->email = @$request->email ?? $user->email;
        $comment->nick = Auth::user()->name ?? $request->name;
        $comment->post_id = $request->post_id;
        $comment->save();
        $gnow = DB::table($table)->where("id",$request->post_id)->pluck("created_at");
        $gnow = str_replace(array('["','"]'),'',$gnow);
        $cid = $comment->id;
        $nick = '';
        \Log::info($request->all());
        $nick = $comment->nick;
        $content = '';

        $content = $comment->content;
        // $MailHelper = NEW MailHelper();
        Mail::to('parie@gmx.de')->send(
            new CommentMail(
                '[MCSL] - Neuer Kommentar auf ' . request()->getHost(),
                'https://' . request()->getHost() . '/admin/tables/comments/show?search=' . $now,
                $nick,
                $content
            )
        );
//         \Log::info("MAIL SENDED");
        // return redirect(url()->previous() . '#comment_' . $request->postid)
        //     ->with('success', 'Kommentar erfolgreich gepostet!');
        $url = url()->previous() . "#comment_{$request->post_id}";
        // \Log::info("TABLE: ".$table.auth()->user()->name);
       // Mail::to('parie@gmx.de')->send(new CommentMail('Asario.de', 'http://localhost:8081/admin/tables/comments/show',auth()->user()->name,$comment->content));
       return response()->json([
        'status' => 'success',
        'comment' => [
            'id' => $cid,
            'author' => $nick,
            'content' => $content,   // 👈 wichtig: gleiche Benennung wie im Frontend
            "profile_photo_path" => Auth::user()->profile_photo_path ?? "008.jpg",
            'created_at' => $now,
        ]
    ]);
    }
    public function save_shp(Request $request)
    {
        $exists = DB::table("shortpoems")->where("headline",$request->words)->exists();
        if(!$exists)
        {
            $id = DB::table('shortpoems')->insertGetId([
                'headline'   => $request->words,
                'users_id'   => Auth::id(),
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
            $email = "shortpoems@asario.de";
            $uhash = Auth::user()->uhash;
            $link = "https://www.asario.de/admin/tables/edit/$id/shortpoems";
            $cont = MCSL_GRAD()."<h2>Nutzer ".Auth::user()->name." hat Wörter für eine Shortpoem gepostet</h2>
                    Die Wörter <b>".$request->words."</b> hat er gewählt,<br /> nu schreib mal was Asario<br /><br />
                    <a class='button-primary' href='".$link."'>Zum Shortpoem</a><br /><br />
                    Viel Spaß";

            $mail = NEW MailController();
            $mail->SendMail("Nutzer ".Auth::user()->name." hat Wörter für eine Shortpoem gepostet", "send", $email,Auth::user()->name, $link, $cont, $uhash);

            return response()->json([
                "status" => "points",
                "message" => "Du hast 5 MCSL Points gesammelt",
            ]);

        }
        return response()->json([
                "status" => "error",
                "message" => "Eintrag exisitiert bereits",
            ]);
    }
public function sendmc(Request $request)
    {
        // Eingeloggte User brauchen kein Captcha & kein "accepted"
        if (Auth::check()) {
            $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);
        } else {
            // Gäste brauchen Captcha + akzeptierte AGB
            $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'captcha' => 'required|string',
                'accepted'=> 'accepted',
            ]);

            // Captcha prüfen
            if (@$_SESSION['captcha_text'] !== $request->captcha) {
                return response()->json(['error' => 'Captcha falsch'], 422);
            }
            unset($_SESSION['captcha_text']);
        }

        // Mail versenden
        try {
            Mail::to(config('mail.maintainer'))->send(new ContactMail(
                $request->getHost(),
                $request->name,
                $request->email,
                $request->subject,
                $request->message
            ));

            return response()->json("1", 200);

        } catch (\Exception $e) {
            \Log::error('Fehler beim Mailversand', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['error' => 'Mail konnte nicht gesendet werden'], 500);
        }
    }


    public function SendMailContent(Request $request){
        return true;
    }
     public function store(Request $request,$table='')
    {
        // if (!auth()->check()) {
        //     return response()->json(['redirect' => route('login')]);
        // }
        // Validierung des Eingabewerts
        $request->validate([
            'comment' => 'required|string|max:1000', // Maximale Länge kann angepasst werden

        ]);

        if($table == "pictures")
        {
            $table = "images";
        }
        if(substr_count($table,"users"))
        {
            $table = "users";
        }
        if(substr_count($table,"blogs"))
        {
            $table = "blogs";
        }
        $user = DB::table("users")->where("id",Auth()->id())->select("email",'name')->first();
        $nick_exists = DB::table("users")->where('name',$request->name)->exists();
        if(!$user && $nick_exists)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Nickname bereits vorhanden, bitte Passwort ausfüllen oder einloggen',
            ]);
        }
        $adtabid = DB::table("admin_table")->where("name",$table)->pluck("id")->first();
        // Kommentar erstellen und in der Datenbank speichern
        $comment = new Comment();
        $comment->content = $request->input('comment2') ?? $request->comment;
        $comment->content = strip_tags($comment->content, '<br>');
        $comment->admin_table_id = $this->GetTid($table);
        $comment->users_id = auth()->id() ?? "7"; // Beispiel für Benutzer-ID
        $comment->nick = Auth::user()->name ?? $request->name;
        $now = now();
        $comment->created_at = $now;
        $comment->updated_at = $now;
        $comment->email = $user->email ?? $request->email;
        $comment->admin_table_id = DB::table("admin_table")->where("name",$table)->value("id");
        $comment->post_id = $request->post_id;
        $comment->save();
        $nick = $user->name ?? $request->name;
        $comment = $comment->content;
        $content = $comment;
        // $MailHelper = NEW MailHelper();
        Mail::to('parie@gmx.de')->send(
            new CommentMail(
                '[MCSL] - Neuer Kommentar auf ' . request()->getHost(),
                'https://' . request()->getHost() . '/' . $table . '/?search=' . $now,
                $nick,
                $content
            )
        );
//         \Log::info("MAIL SENDED");
        // $MailHelper->SendMailer("parie@gmx.de","Neuer Kommentar auf www.asario.net","",'','','','newcomment',["name"=>$nick,"table"=>$table,"comment"=>$comment]);



return response()->json([
    'status' => 'success',
    'message' => 'Kommentar erfolgreich gepostet!',
    'comment' => $comment
]);
    }
    // public function checkComment(Request $request){
    //     $id = $request->id;

    //     $res = DB::table("users_rights")->leftJoin("users","users.users_rights_id","=","users_rights.id")->where("users.id",Auth::id())->select("users_rights.unique_id")->get();

    //     $userRight = DB::table("users_rights")
    //         ->join("users", "users.users_rights_id", "=", "users_rights.id")
    //         ->where("users.id", Auth::id())
    //         ->value("users_rights.unique_id");

    //     $commentSeen = DB::table('comments')
    //         ->where("comments.id",$id)
    //         ->whereRaw('ischecked & ? > 0', [$userRight])
    //         ->exists();
    //         \Log::info("cs:".json_encode($commentSeen)) ;
    //         if(!$commentSeen){
    //             DB::table("comments")->update([
    //                 'ischecked' => DB::raw("ischecked + " . $res->unique_id)
    //             ]);
    //         }

    //         \Log::info("aff:".json_encode($res,JSON_PRETTY_PRINT));
    //     return true;
    // }
    // public function checkComment(Request $request)
    // {
    //     $id = $request->id;

    //     // Rechte-ID des aktuellen Users holen
    //     $userRight = DB::table("users_rights")
    //         ->join("users", "users.users_rights_id", "=", "users_rights.id")
    //         ->where("users.id", Auth::id())
    //         ->value("users_rights.unique_id");

    //     if (!$userRight) {
    //         \Log::warning("Keine Rechte für User ".Auth::id());
    //         return response()->json(['error' => 'Keine Rechte vorhanden'], 403);
    //     }
    //     $ids = DB::table("comments")->pluck("id");
    //     foreach($ids as $id){
    //     // Prüfen, ob das Kommentarfeld schon als gelesen markiert wurde
    //     $commentSeen = DB::table('comments')
    //         ->where("comments.id", $id)
    //         ->whereRaw('ischecked & ? > 0', [$userRight])
    //         ->exists();

    //     \Log::info("Kommentar $id schon gesehen? " . ($commentSeen ? 'ja' : 'nein'));
    //     \Log::info("UR:".json_encode($request->input('ids')));

    //     if (!$commentSeen) {
    //         // Kommentar aktualisieren (nur EINEN!)
    //         DB::table("comments")
    //             ->where("id", $id)
    //             ->update([
    //                 'ischecked' => DB::raw("ischecked + " . $userRight)
    //             ]);
    //     }
    // }
    //     return response()->json(['success' => true]);
    // }
    public function checkComment_old()
{
    try {
        $this->updateComments();
        return response()->json(['success' => 'Kommentare aktualisiert']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 504);
    }
}
    public function checkComment()
    {
        $userId = Auth::id();

    if (!$userId) {
        // throw new \Exception('User not authenticated');
    }
    if(!Schema::hasTable("comments")){
        return;
    }

    // // 1. Hole alle unique_id Werte aus users_rights, die zum angemeldeten User gehören
    // $newFlags = DB::table('users')
    //     ->leftJoin('users_rights', 'users.users_rights_id', '=', 'users_rights.id')
    //     ->where('users.id', $userId)
    //     ->pluck('unique_id')
    //     ->map(fn($v) => (int)$v)
    //     ->toArray();
    // // \Log::info("NF:".json_encode($newFlags,JSON_PRETTY_PRINT));
    // if (empty($newFlags)) {
    //     // Keine Rechte gefunden, ggf. Abbruch oder nichts machen
    //     return;
    // }

    // 2. Hole alle Kommentare
    $comments = DB::table('comments')->select('id', 'ischecked')->get();
    // \Log::info("com:".json_encode($comments,JSON_PRETTY_PRINT));
    // 3. Für jeden Kommentar prüfen und ischecked aktualisieren
    foreach ($comments as $comment) {
        // $currentIschecked = (int)$comment->ischecked;

        // foreach ($newFlags as $flag) {
        //     // Prüfen ob flag schon komplett gesetzt ist
        //     if ((($currentIschecked & $flag) === $flag) && $currentIschecked != 0) {
        //         continue;

        //     }
        //     \Log::info("cic:".json_encode($currentIschecked,JSON_PRETTY_PRINT));
        //     // Flag hinzufügen (Bitwise OR)
        //     $currentIschecked = $flag+$currentIschecked;
            $com[$comment->id] = $comment->ischecked;

        // // Update, falls Wert sich geändert hat
        DB::table('comments')
                ->where('ischecked', '0')
                ->update([
                    'ischecked' =>"1",
                    'checked_at' => now(),
                ]);

        }
        return response()->json(["success"=>$com]);
    }



    //     $userId = Auth::id();
    //     $user = User::with('rights')->findOrFail($userId); // load users_rights relation
    //     $userRights = $user->rights; // users_rights record

    //     if (!$userRights) {
    //         return 'User has no rights assigned.';
    //     }

    //     // Wenn unique_id bereits 41 (Developer) ist → nichts tun
    //     if ($userRights->unique_id == 41) {
    //         return 'User already has Developer rights.';
    //     }

    //     // Wenn unique_id == 42 (Admin) → Developer-Rechte dazunehmen
    //     if ($userRights->unique_id == 42) {
    //         $adminRights = $userRights;
    //         $developerRights = UsersRight::where('unique_id', 41)->first();

    //         if (!$developerRights) {
    //             return 'Developer rights (unique_id=41) not found.';
    //         }

    //         // Nehmen wir an, rights sind als Bitstring oder Integer gespeichert
    //         // Beispiel mit Integer-Rechten:
    //         $mergedRights = $adminRights->rights | $developerRights->rights;

    //         // Jetzt: entweder neues Rights-Profil speichern oder User überschreiben
    //         // Beispiel: override direkt beim User (wenn du ein Feld z. B. `effective_rights` hast)
    //         $user->effective_rights = $mergedRights;
    //         $user->save();

    //         return 'Admin rights merged with Developer rights.';
    //     }

    //     return 'No action required.';
    // }

    function CheckCommentsDone(Request $request){
        $ids = $request->input('ids', []);

        // Rechte des eingeloggten Nutzers holen
        $userRight = DB::table('users_rights')
            ->join('users', 'users.users_rights_id', '=', 'users_rights.id')
            ->where('users.id', Auth::id())
            ->value('users_rights.unique_id');

        if (!$userRight) {
            return response()->json([]);
        }

        // Alle Kommentare abrufen, bei denen das Recht gesetzt ist
        $checkedComments = DB::table('comments')
            ->whereIn('id', $ids)
            ->whereRaw('ischecked & ? > 0', [$userRight])
            ->pluck('id')
            ->toArray();

        // Ergebnis als Map zurückgeben: id => true/false
        $result = [];
        foreach ($ids as $id) {
            $result[$id] = in_array($id, $checkedComments);
        }
        return response()->json($result);
    }
    public function fetchComments(Request $request,$table,$postId='')
    {

        $table = $request->table;
        $path = request()->path(); // Gibt "home/show/images/search/Fasermaler"
                $parts = explode("/", $path);

                if(substr_count($table,"users"))
                {
                    $table = "users";
                }
                if(substr_count($table,"blogs"))
                {
                    $table = "blogs";
                }

                if($table == "pictures")
                {
                    $table = "images";
                }
                $table_alt = $table;
                // $sa = Settings::$searchable;
                // \Log::info($parts);
                // foreach($sa as $ta)
                // {
                //     if(in_array($ta,$parts))
                //     {
                //         $table = $ta;
                //         $table_alt  = $ta;
                //         $_GET['table'] = $ta;
                //         \Log::info("ssadddddddddddddd".$table);
                //     }
                //     else
                //     {
                //         continue;
                //     }

                // }
        $adtabid = DB::table("admin_table")->where("name",$table)->pluck("id")->first();
        DB::enableQueryLog();
            $comments = Comment::where('post_id', $postId)
            ->leftJoin('users', 'comments.users_id', '=', 'users.id')
            ->select("comments.*", "users.profile_photo_path", "comments.nick as author")
            ->where("admin_table_id",$this->GetTid($table_alt))
            ->orderBy("id","DESC")->latest()->get();
            // \Log::info(DB::getQueryLog());
            //  \Log::info("TABLE:".$table);
             if(!$comments){
                //return [];
             }
        return response($comments);
    }
    public function GetTid($tab)
    {
        return DB::table("admin_table")->where("name",strtolower($tab))->pluck("id")->first();
    }
    public static function ComForm($table,$comments,$post)
    {

               // \Log::info("TABLE:".$table);
                $offset = request()->get('offset', -10);
                // $offset = @$_GET['offset'] ?? $offset;

                $limit = 10;
                //$offset = $offset-10;

                $comments[$post->id] = DB::table('comments')
                    ->join('users', 'comments.users_id', '=', 'users.id')
                    ->where('comments.post_id', '=', $post->id)
                    ->orderBy('comments.id', 'DESC')
                    ->select('users.name', 'users.image_path', 'comments.*')
                    ->take($limit)
                    ->offset($offset)
                    ->get();
        return view('posts.partials.comment_form', [
        'table'=> $table,
        'post' => $post,
        'comments' => $comments
    ])->render();
    }
    public function fetchComments_alt_old(Request $request)
    {
        $postId = $request->post_id;
        $offset = $request->offset ?? 0; // Startpunkt für das Paging
        $limit = 10; // Anzahl der Kommentare pro Anfrage

        // Kommentare abrufen
        $comments = DB::table('comments')
            ->join('users', 'comments.users_id', '=', 'users.id') // Join mit der Tabelle users
            ->where('comments.post_id', $postId)
            ->where('comments.tablename', 'images')
            ->orderBy('comments.created_at', 'desc')
            ->select('comments.*', 'users.name','users.image_path') // Auswahl von Feldern aus beiden Tabellen
            ->skip($offset)
            ->take($limit)
            ->get();
        return response()->json([
            'comments' => $comments,
        ]);
    }
public function fetchComments_alt(Request $request)
{
    $postId = $request->query('post_id');
    $offset = (int)$request->query('offset', 0); // Standardwert für Offset
    $limit = 10; // Anzahl der Kommentare pro Anfrage
    $processedIds = session()->get('comment_ids', []);

    // Hole die Kommentare und verbinde sie mit den Benutzerdaten
    $comments = DB::table('comments')
        ->join('users', 'comments.users_id', '=', 'users.id')
        ->where('comments.post_id', $postId)
        ->whereNotIn('comments.id', $processedIds)
        ->select(
            'comments.id',
            'users.name',
            'users.image_path',
            'comments.content',
            'comments.created_at'
        )
        ->orderBy('comments.id', 'desc') // Neuester Kommentar zuerst
        ->offset($offset)
        ->limit($limit)
        ->get();
        $processedIds = session()->get('comment_ids', []);
        $newIds = $comments->pluck('id')->toArray();
        session()->put('comment_ids', array_merge($processedIds, $newIds));
        // return response()->json(["comments"=>session()->get('comment_ids')]);
        foreach($newIds AS $nid)
        {
            if(!in_array($nid->id,$processedIds))
            {

            }

        }
        return response()->json(['comments' => $comments]);



}

public function fetchComments_alt2(Request $request)
{
    // Initialisierung der Variablen
    $em = 0;


    // Eingabewerte
    $postId = $request->query('post_id');
    $offset = (int)$request->query('offset', 0); // Standardwert für Offset
    $limit = 10; // Anzahl der Kommentare pro Anfrage

    // Hole die Kommentare und verbinde sie mit den Benutzerdaten
    $comments = DB::table('comments')
        ->join('users', 'comments.users_id', '=', 'users.id')
        ->where('comments.post_id', $postId)
        ->select(
            'users.name',
            'users.image_path',
            'comments.content',
            'comments.id'
        )
        ->orderBy('comments.created_at', 'desc') // Neuester Kommentar zuerst
        ->offset($offset)
        ->limit($limit)
        ->get();

    // Überprüfe, ob der Offset bereits verarbeitet wurde
    if (!in_array($offset, $this->ofst) || !is_array($this->ofst)) {
        $this->ofst[] = $offset; // Offset speichern, wenn noch nicht vorhanden
        session()->put('offsets', $ofst); // Speichere das Offset-Array in der Session
    } else {
        $em++; // Zähler erhöhen, wenn der Offset schon verarbeitet wurde
    }



    // Überprüfe, ob Kommentare vorhanden sind oder ob wir bereits alle Kommentare geladen haben
    if ($comments->isEmpty() || $em > 0) {

        return response()->json(['comments' => []]); // Keine weiteren Kommentare
    } else {
        return response()->json(['comments' => $comments]); // Gebe die geladenen Kommentare zurück
    }
}
public function com_loadMore(Request $request,$tid='')
    {
       // \Log::info("req:".json_encode($tid));
        $offset = $request->input('offset',0);
        $limit = 10;
        $id = $tid;
        $offset = (int) $offset;
        //$offset = $offset > 0 ? ($offset-10) : $offset;
        // \Log::info($offset);
        $limit = $request->get('limit', 10);
        // Weitere Beiträge laden
        $posts = DB::table('comments')
            ->select('comments.*', 'users.name','users.image_path','blog_posts.*') // Wähle Spalten aus comments und users
            ->join('users', 'comments.users_id', '=', 'users.id') // Join mit users
            ->join('blog_posts', function ($join) {
              $join->on('comments.tablename', '=', DB::raw("'blog_posts'")); // Text "Blog_posts"
})
            ->joinSub(
                DB::table('comments')
                    ->select('post_id', DB::raw('MAX(created_at) as latest_created_at')) // max(created_at)
                    ->where('post_id', $tid) // Filter für den Post
                    ->where('pub', 1) // Nur veröffentlichte Kommentare
                    ->groupBy('post_id'), // Gruppiere nach post_id
                'subquery', // Subquery Alias
                function ($join) {
                    $join->on('comments.post_id', '=', 'subquery.post_id') // Verbinde mit comments
                        ->on('comments.created_at', '=', 'subquery.latest_created_at'); // Nur die neuesten
                }
            )
            ->orderBy('subquery.latest_created_at', 'desc') // Sortiere nach neuesten Kommentaren
            ->skip($offset) // Pagination: Offset
            ->take($limit) // Pagination: Limit
            ->get();



        // $postIds = DB::table('comments')->pluck('post_id')->first(); // Pluck gibt ein Array mit den IDs zurück
        $postIds = DB::table('comments')
            ->groupBy('post_id')
            ->pluck('post_id');
        $data = $posts;
        // foreach ($postIds as $id) {
        // $comments[$id => []]; // Initialisiere das Array
        $ratings[$id] = DB::table('ratings')
            ->where('image_id', $id) // Ersetze $id mit der gewünschten image_id
            ->selectRaw('COALESCE(AVG(rating), 0) as a_rating')
            ->pluck('a_rating');
    // $newIds = DB::table('comments')
    //     ->join('users', 'comments.users_id', '=', 'users.id')
    //     ->where('comments.post_id', '=', $id)
    //     ->pluck('comments.id'); // Hol nur die `id`-Spalte


    // $processedIds = array_merge($newIds,$processedIds);  // Füge die neue ID zu den verarbeiteten IDs hinzu

    // session()->put('processedIds', $processedIds);
    // $commentsQuery[$id] = DB::table('comments')
    //     ->join('users', 'comments.users_id', '=', 'users.id')
    //     ->where('comments.post_id', '=', $id)
    //     ->whereNotIn('comments.id', $this->processedIds)
    //     ->orderBy("comments.created_at", "desc")
    //     ->select('users.name', "users.image_path", 'comments.*')
    //     ->get();
    // $comments = [
    //     $id => $commentsQuery[$id]->unique('comments.id')
    // ]; // Filtere eindeutige Einträge basierend auf der Kommentar-ID
    DB::enableQueryLog();
    $comments = [
        $id => DB::table('comments')
            ->join('users', 'comments.users_id', '=', 'users.id')
            ->where('comments.post_id', '=', $id)
            ///->whereNotIn('comments.id', $this->processedIds) // IDs aus processedIds ausschließen
            ->orderBy("comments.id", "desc") // Sortierung nach Erstellungsdatum
            ->select('users.name', 'users.image_path', 'comments.*') // Gewünschte Spalten auswählen
            ->take($limit)
            ->offset($offset)
            ->get() // Daten abrufen
    ];
    // \Log::info(DB::getQueryLog());
// \Log::info("com_$id:".$comments[$id]);
// }
    return response()->json(compact('comments'));

}
    public function destroy($postid,$id,$table,$pic='')
    {
        if(!CheckRights(Auth::id(),"comments","delete"))
        {
            return redirect()->route('tables.noview');
        }

        // $post = DB::table('comments')->where("id",$id)->first();
        // $post->delete();
        $post = Comment::find($id);

        if ($post) {
            $post->delete();
        }
    //     $tab = $table ?? "images";
    //     \Log::info("pic:".$pic);
    //     $pic = "acryl";
    //     $redir = $tab == "images" ? "pictures" : "blgoposts.index";
    //     return redirect()->route($tab, ['open_comments' => $postid,'pic'=>$pic])
    // ->with('success_'.$postid, 'Kommentar erfolgreich gelöscht!')
    // ->header('Location', redirect()->route($redir, ['pic'=>$pic,'open_comments' => $postid])->getTargetUrl() . "#cmtbtn-$postid");
    $tab = $table ?? "images";
    // \Log::info("pic:". $pic. "tab:".$tab);

    // $pic = "acryl"; // Sicherstellen, dass $pic definiert ist
    $redir = $tab == "images" || $tab == 'pictures' ? "pictures" : "blogposts.index";

    return redirect()->route($tab, [
            'open_comments' => $postid,
            'pic' => $pic // pic hier explizit setzen
        ])
        ->with('success_' . $postid, 'Kommentar erfolgreich gelöscht!')
        ->header('Location', route($redir, [
            'pic' => $pic, // pic auch hier setzen
            'open_comments' => $postid
        ]) . "#cmtbtn-$postid");

    }
    public function destroy_comments($comments_id)
    {
        if(!CheckRights(Auth::id(),"comments","delete"))
        {
            return redirect()->route('tables.noview');
        }

        // $post = DB::table('comments')->where("id",$id)->first();
        // $post->delete();
        $post = Comment::find($comments_id);

        if ($post) {
            $post->delete();
        }
    //     $tab = $table ?? "images";
    //     \Log::info("pic:".$pic);
    //     $pic = "acryl";
    //     $redir = $tab == "images" ? "pictures" : "blgoposts.index";
    //     return redirect()->route($tab, ['open_comments' => $postid,'pic'=>$pic])
    // ->with('success_'.$postid, 'Kommentar erfolgreich gelöscht!')
    // ->header('Location', redirect()->route($redir, ['pic'=>$pic,'open_comments' => $postid])->getTargetUrl() . "#cmtbtn-$postid");
    $tab = $table ?? "images";
    // \Log::info("pic:". $pic. "tab:".$tab);

    // $pic = "acryl"; // Sicherstellen, dass $pic definiert ist
    $redir = $tab == "images" || $tab == 'pictures' ? "pictures" : "blogposts.index";

    // return redirect()->route($tab, [
    //         'open_comments' => $postid,
    //         'pic' => $pic // pic hier explizit setzen
    //     ])
    //     ->with('success_' . $postid, 'Kommentar erfolgreich gelöscht!')
    //     ->header('Location', route($redir, [
    //         'pic' => $pic, // pic auch hier setzen
    //         'open_comments' => $postid
    //     ]) . "#cmtbtn-$postid");

    }
public function loadComments(Request $request, $postId)
{
    $take = $request->get('take', 10); // Anzahl der zu ladenden Kommentare
    $limit = 10;
    $offset = $request->get('offset', 0);
    $processedIds = session()->get('processedIds', []); // Falls nicht vorhanden, ein leeres Array zurückgeben

    // Neue ID, die verarbeitet wurde
    $newId = $postId;

    // Wenn die neue ID noch nicht in den verarbeiteten IDs ist, füge sie hinzu
    if (!in_array($newId, $processedIds)) {
        $processedIds[] = $newId;  // Füge die neue ID zu den verarbeiteten IDs hinzu
    }

    // Speichern der aktualisierten 'processedIds' in der Session
    session()->put('processedIds', $processedIds);

    // Hole Kommentare und schließe diejenigen aus, deren IDs in $processedIds enthalten sind
    $comments = Comment::join('users', 'comments.users_id', '=', 'users.id')
        ->where('comments.post_id', '=', $postId)
        ->whereNotIn('comments.id', $processedIds) // Exkludiere Kommentare mit IDs in $processedIds
        ->orderBy('comments.created_at', 'desc')
        // ->select('users.name','users.image_path', 'comments.*')
        ->take($limit)
        ->offset($offset)
        ->get(); // Abfrage ausführen und Ergebnisse holen

    // Lade den Post
    $posts = Post::find($postId);
    $hasMore = Comment::where('post_id', $postId)
        ->count() > ($offset + $take);

    return response()->json([
        'comments' => $comments,
        'hasMore' => $hasMore,
    ]);
}
public function loadComments_oldd(Request $request)
{
    $request->validate([
        'post_ids' => 'required|array',
        'post_ids.*' => 'integer',
        'page' => 'nullable|integer|min:1',
    ]);

    $postIds = $request->post_ids;
    // $comments = Comment::whereIn('post_id', $postIds)
    //     ->orderBy('created_at', 'desc')
    //     ->paginate(10); // 10 Kommentare pro Seite
    $comments = Comment::whereIn('post_id', $postIds)->get()->groupBy('post_id');

    return response()->json([
        'comments' => $comments->items(),
        'next_page' => $comments->nextPageUrl(),
    ]);
}







}
