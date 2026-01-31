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
class HomeController_mfx extends Controller
{
    //

    public function home_index(){
        return $this->home();
    }
    public function home()
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
    public static function home_mfx()
    {
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
    public function infos_show($id){
        $data = DB::table("infos")->where("pub","1")->where("id",$id)->select("id","headline","message","img_big")->orderBy("position","DESC")->first();
        return Inertia::render('Homepage/mfx/infos_show',compact('data'));
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
    public function mcsl_changelog()
    {
        return Inertia::render('Homepage/mfx/changelog');
    }
    public function home_usershow($username)
    {
        $users = DB::table("users")->where("name",$username)->where("pub","1")->where("xis_disabled","0")->select("users.*")->first();
        // \Log::info("HU:".json_encode($users,JSON_PRETTY_PRINT));
        return Inertia::render('Homepage/Usershow', [
            'users' => $users, // statt 'data'
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
    //
    public function home_privacy()
    {
        $privacyFile = Jetstream::localizedMarkdownPath('privacy.md');
        $privacy = Str::markdown(file_get_contents($privacyFile)); // HTML erzeugt

        return Inertia::render('Homepage/Privacy', [
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


}
