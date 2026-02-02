<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
class MCSLPointsController extends Controller
{
    //
    var $ccnt;
    var $scnt;
    var $ncnt;

    function __construct()
    {
        // Comments
        $this->ccnt = 3;
        // Stars/Rating
        $this->scnt = 1;
        // Newsletter read
        $this->ncnt = 8;
        $this->SPcnt = 5;
    }
    function SendMail(Request $request)
    {
        $row = (array) DB::table('images')
            ->leftJoin(
                'image_categories',
                'images.image_categories_id',
                '=',
                'image_categories.id'
            )
            ->where('images.id', $request->img_id)
            ->select(
                'images.preis',
                'images.name',
                'images.image_path',
                DB::raw('image_categories.name as kategorie'),
                DB::raw('images.image_path as bildname')
            )
            ->first();
        $uhash = Auth::user()->uhash;
        $link = '';
        $email = "parie@gmx.de";
        $row['nick'] = Auth::user()->name;
        $row['punkte'] = $row['preis']*3;
        $cont = '<div style="font-family:Tahoma, Geneva, Verdana, sans-serif !important;">'.MCSL_GRAD().'

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden;font-family:"Segoe UI", Tahoma, Geneva, Verdana, sans-serif !important;"
    >
        <tr>
            <td style="padding:20px; border-bottom:1px solid #e5e5e5;">
                <h2 style="margin:0; color:#333;">
                    Neue Prämienauswahl
                </h2>

                <p style="margin:8px 0 0; color:#666;">
                    Der Nutzer <strong>'.$row['nick'].'</strong> hat eine Prämie ausgewählt.
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding:20px;">
                <h3 style="margin-top:0; color:#333;">
                    Prämiendetails
                </h3>

                <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="border-bottom:1px solid #eee; color:#555;">
                            <strong>Bildname</strong>
                        </td>
                        <td style="border-bottom:1px solid #eee;">
                            '.$row['name'].'
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom:1px solid #eee; color:#555;">
                            <strong>Kategorie</strong>
                        </td>
                        <td style="border-bottom:1px solid #eee;">
                            '.ucfirst($row['kategorie']).'
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom:1px solid #eee; color:#555;">
                            <strong>Benötigte Punkte</strong>
                        </td>
                        <td style="border-bottom:1px solid #eee;">
                            '.$row['punkte'].' MCSL Points
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom:1px solid #eee; color:#555;">
                            <strong>Aktueller Punktestand</strong>
                        </td>
                        <td style="border-bottom:1px solid #eee;">
                            '.$this->GetCount($request).' MCSL Points
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom:1px solid #eee; color:#555;">
                            <strong>User-ID</strong>
                        </td>
                        <td style="border-bottom:1px solid #eee;">
                            '.Auth::id().'
                        </td>
                    </tr>

                    <tr>
                        <td style="color:#555;">
                            <strong>Auswahlzeitpunkt</strong>
                        </td>
                        <td>
                            '.date("d.m.Y H:i:s").'
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding:20px;">
                <h3 style="margin-top:0; color:#333;">
                    Bildvorschau
                </h3>

                <img
                    src="https://www.asario.de/images/_ab/images/image_path/thumbs/'.$row['image_path'].'"
                    alt="'.$row['bildname'].'"
                    style="width:100%; max-width:400px; border-radius:6px; border:1px solid #ddd;"
                >
            </td>
        </tr>

        <tr>
            <td style="padding:20px; background:#fafafa; border-top:1px solid #e5e5e5;">
                <p style="margin:0; font-size:13px; color:#777;">
                    Bitte prüfen Sie die Auswahl und leiten Sie die weitere Bearbeitung ein
                    (Freigabe, Versand oder Rückmeldung an den Nutzer).
                </p>

                <p style="margin:10px 0 0; font-size:12px; color:#aaa;">
                    MCSL System – automatische Benachrichtigung
                </p>
            </td>
        </tr>
    </table>';
        // echo $cont;
        // return;
        $mail = NEW MailController();
        $mail->SendMail("Nutzer ".$row['nick']." hat eine Prämie ausgweählt", "send", $email,$row['nick'], $link, $cont, $uhash);
        return Inertia::render('Components/Social/PictureWishesSended');
    }
    function SelectPremiums()
    {
        $request = new Request();
        $request->merge(['users_id' => Auth::id()]);

        DB::enableQueryLog();

        $images = DB::table('images')
        ->join(
            'image_categories',
            'images.image_categories_id',
            '=',
            'image_categories.id'
        )
        ->where('images.xis_IsSaleable', 1)
        ->where('images.preis', '<', 100)
        ->where("status","forsale")
        // ->where("preis","<",($this->getCount($request)/3))
        ->select(
            'images.id',
            'images.name',
            'images.preis',
            'images.image_path',
            'images.xis_IsSaleable',
            'images.image_categories_id',
            'image_categories.id as category_id',
            'image_categories.name as category_name',
            'images.img_x as img_x',
            'images.img_y as img_y',
        )
        ->orderBy('image_categories.name')
        ->orderBy('images.name')
        ->get()
        ->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'preis' => (int) $row->preis,
                'img_x' => $row->img_x,
                'img_y' => $row->img_y,
                'image_path' => $row->image_path,
                'xis_IsSaleable' => (int) $row->xis_IsSaleable,
                'image_categories_id' => $row->image_categories_id,
                'image_category' => [
                    'id' => $row->category_id,
                    'name' => $row->category_name,
                ],
            ];
        })
        ->values(); // saubere JSON-Arrays
        // dd(DB::getQueryLog());

        return Inertia::render('Components/Social/SelectPremiums',['images'=>$images,"MCSL_Total"=>$this->GetCount($request),"users_id"=>Auth::id()]);
    }
    function admin_index(Request $request)
    {
        $text = DB::table("texts")->where("autoslug","yfh7")->first();
        $nick = DB::table("users")->where("id",$request->users_id ?? Auth::id())->value('name');
        // dd($nick);
        return Inertia::render('Components/Social/McsStatsChart',
        ['count_mcs'=>$this->GetCount($request),
        "count_com"=>$this->GetCommentsCount($request),
        "mcslpoints"=>$this->Getmcslpoints($request),
        "commentsStats"=>$this->GetComments($request),
        "ratingsStats"=>$this->GetRatings($request),
        "activity"=>$this->GetUserActivity($request),
         'stackedUserStats' => $this->GetUserActivity($request),
         "MCSL_GLOB_PTS"=>$this->GetCount($request,"1"),
        "text"=>$text,
        "MCS_POINTS_TOTAL"=>$this->GetAllPoints($request),
        "nick"=>$nick,
           'breadcrumbs' => [
            'MCSL Points Center' => route('admin.mcslpoints'),
        ],
        ]);

    }
    function GetAllPoints(Request $request)
    {
        $users = DB::table("users")->where("xis_disabled","0")->select("id")->get();
        $cnt = 0;
        foreach($users as $user)
        {
            $request = new Request();
            $request->merge(['users_id' => $user->id]);
            $cnt = $cnt + $this->GetCount($request);
        }
        return $cnt;
    }
    function GetUserActivity(Request $request)
    {
        $users = DB::table("users")
        ->where("xis_disabled","0")
        ->select("id","name")
        ->orderBy("name","ASC")
        ->get()
        ->map(function($u) {
            return (array) $u;
        })->toArray();;
       // dd($users);
        foreach($users as $user)
        {
            $request = new Request();
            $request->merge(['users_id' => $user['id']]);
            $comme_con[$user['id']] = $this->GetCommentsCount($request);
            $ratin_con[$user['id']] = $this->GetRatingsCount($request);
            $shpoe_con[$user['id']] = $this->GetShortPCount($request);
            $newsl_con[$user['id']] = $this->GetNewslCount($request);

            $nick[$user['id']] = $user['name'];
            if(!empty($comme_con[$user['id']]) || !empty($ratin_con[$user['id']]) || !empty($shpoe_con[$user['id']]) || !empty($newsl_con[$user['id']]))
            {
                $nickz[$user['id']] = $user['name'];
            }


        }
        $labels = [];
$comments = [];
$ratings = [];
$shortpoems = [];
$newsletter = [];

foreach ($nickz as $userId => $nick) {
    $labels[] = $nick;

    $comments[]   = (int) ($comme_con[$userId]   ?? 0);
    $ratings[]    = (int) ($ratin_con[$userId]  ?? 0);
    $shortpoems[] = (int) ($shpoe_con[$userId]  ?? 0);
    $newsletter[] = (int) ($newsl_con[$userId]  ?? 0);
}

$stackedStats = [
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'Kommentare',
            'data' => $comments,
        ],
        [
            'label' => 'Ratings',
            'data' => $ratings,
        ],
        [
            'label' => 'Shortpoems',
            'data' => $shortpoems,
        ],
        [
            'label' => 'Newsletter',
            'data' => $newsletter,
        ],
    ],
];
return $stackedStats; // dd($comme_con[5]);

    }
    function GetRatingsCount(Request $request)
    {
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        return DB::table("ratings")->where("users_id",$users_id)->count();
    }
    function GetRatings(Request $request)
    {
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        $ratingsStatsRaw = DB::table('ratings')
        ->where("users_id",$users_id)
        ->select(
            DB::raw('`table` as table_name'),
            DB::raw('COUNT(id) as total')
        )
        ->groupBy('table_name')
        ->orderBy('table',"ASC")
        ->get();
        $ratingsStats = [
        'labels' => [],
        'values' => [],
    ];

    foreach ($ratingsStatsRaw as $row) {
        $ratingsStats['labels'][] = ucfirst($row->table_name);
        $ratingsStats['values'][] = $row->total;
    }
    return $ratingsStats;
    }
    function GetComments(Request $request)
    {

        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
       $commentsStats = [
    'blogs'        => 0,
    'didyouknow'  => 0,
    'images'      => 0,
    'shortpoems'  => 0,
];

// Query: Kommentare nach admin_table.name gruppieren
$commentsStatsRaw = DB::table('comments')
    ->join('admin_table', 'comments.admin_table_id', '=', 'admin_table.id')
    ->where("comments.users_id",$users_id)
    ->select('admin_table.name', DB::raw('COUNT(comments.id) as total'))
    ->groupBy('admin_table.name')
    ->get();

// Rohdaten ins fertige Array übertragen
foreach ($commentsStatsRaw as $stat) {
    if (array_key_exists($stat->name, $commentsStats)) {
        $commentsStats[$stat->name] = $stat->total;
    }
}
// dd($commentsStatsRaw->toArray());

        return $commentsStats;
    }
    function Getmcslpoints(Request $request)
    {
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        $stars = DB::table("ratings")->where("users_id",$users_id)->count();
        $comms = DB::table("comments")->where("users_id",$users_id)->count();
        $newsl = DB::table("points")->where("users_id",$users_id)->sum('points') ?? 0;

        $shopo  = DB::table("shortpoems")->where("users_id",$users_id)->count();
        $res = [
            'comments'    => $comms*$this->ccnt,
            'newsletter'  => $newsl,
            'ratings'     => $stars*$this->scnt,
            'shortpoems'  => $shopo*$this->SPcnt,
        ];
        return $res;
    }
    function GetCommentsCount(Request $request){

        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        $cnt = DB::table("comments")->where("users_id",$users_id)->count();

        if ($users_id === null) {
            $cnt = 0;
        }
        return $cnt;
    }
    function GetStarsCount(Request $request)
    {
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        if (!Schema::hasTable('ratings')) {
            return;
        }
        $cnt = DB::table("ratings")->where("users_id",$users_id)->count();
        if ($users_id === null) {
            $cnt = 0;
        }
        return $cnt;
    }
    function GetShortPCount(Request $request)
    {
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        $cnt = DB::table("shortpoems")->where("users_id",$users_id)->count();
        if ($users_id === null) {
            $cnt = 0;
        }
        return $cnt;
    }
    function GetNewslCount(Request $request)
    {
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }
        $cnt = DB::table("points")->where("users_id",$users_id)->sum("points");
        if ($users_id === null) {
            $cnt = 0;
        }
        return @$cnt ?? 0;
    }
    function GetCount(Request $request,$uid='')
    {
        if(!Schema::hasTable("comments") || !Schema::hasTable("ratings")){
            return;
        }
        $users_id = $request->users_id;
        if(empty($users_id) || $uid == "1")
        {
            $users_id = Auth::id();
             $request = new Request();
            $request->merge(['users_id' => Auth::id()]);
        }

        $cnt = $this->GetCommentsCount($request)*$this->ccnt;
        $cnt = $cnt + ($this->GetStarsCount($request)*$this->scnt);
        $cnt = $cnt + ($this->GetShortPCount($request)*$this->SPcnt);
        $cnt = $cnt + ($this->GetNewslCount($request));
        // dd($cnt);

        return $cnt;
    }
}
