<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // <-- WICHTIG!
use App\Mail\GeneralMail; // <-- WICHTIG!
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\IMULController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\FormController;
use App\Helper\CustomHelpers;
use App\Models\Settings;
use App\Models\Table;
use App\Models\AdminTable;
use App\Models\UsersRight;
use App\Services\Inkrementierer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Factories\HasFactory;
if(!session_id()){
        session_start();
    }

class MailController extends Controller
{
    public function __construct()
    {
        GlobalController::SetDomain();

    }
    public function Subscribe_Newsl(Request $request)
    {
        if(empty($request->email)){
            return false;
        }
        if(empty($request->firstname) && empty($request->lastname))
        {
            $request->firstname = '';

        }
        $uhash = $this->randomString64();
        DB::table("newsletter_reci")->insert([
            "pub"=>0,
            "userhash"=> $uhash,
            "email"=>$request->email,
            "nick"=>$this->Quote($request->email),
            "title"=>$request->title,
            "prename"=>$request->firstname,
            "surname"=>$request->lastname,
        ]);
        $this->SendMail("Newslsetter Anmeldung","emails.newslsub",$request->email,$request->title." ".$request->firstname." ".$request->lastname,"http://".request()->getHost()."/mail/subscribe/".$uhash."/".$request->email,'');
    }
    //return $ma->PrevMail("[MCSl] Newsletter","emails.newsletter",$email,$nick,$link,$content,$signatur);
    function PrevMail(Request $request,$title,$template,$email,$nick,$link,$content,$signatur){


    $footer = $signatur;

    $signatur = $this->replink($signatur,$title,$email,$nick,$link);

    $content = $this->replink($content,$title,$email,$nick,$link);

    $signatur2 = $signatur.$this->subm_btn();
        $content_alt = $content.$signatur;
    $data = [$title,$link,$nick,$content,$template,$footer];
    $html = html_entity_decode(View::file(resource_path('views/vendor/mail/html/preview.blade.php'), compact('title','link','nick','content','template','signatur2'))->render());

    $html2 = html_entity_decode(
        View::file(
            resource_path('views/vendor/mail/html/send.blade.php'),
            compact('title', 'link', 'nick', 'content_alt', 'template', 'signatur'))->render()
    );
    session(['signatur' => $signatur,"reci"=>$request->recipients, "content"=>html_entity_decode($html2), "title"=>$title,"email"=>$email,"nick"=>$nick,"template"=>$template]);
    return $html;


    // HTML rendern
    // $html = View::make('email',$data)->render();

    // return response()->json(['html' => $html]);

//        return View::make('vendor.notifications.email',);
    }
    function subm_btn()
    {
        return "<br /><br /><a href='/email/send/'>E-mail Senden</a>";
    }
    function send_newsletter(){
        $i = 0;
        foreach (explode(", ",session("reci")) as $rek)
        {
            $res = DB::table("users")->where("name",$rek)->select("email","uhash","name")->first();
            // dd(session('reci'));
            $uhash = @$res->uhash;
            $email = @$res->email;
            $nick = @$res->name;
        $this->SendMail(session('title'),session('template'),$email,$nick,'',html_entity_decode(session('content')));
        $i++;
        }




       return Inertia::render("Components/Social/Emails_Sended",["i"=>$i]);

    }
    function replink($con,$title,$email,$nick,$link=''){
        $l = '{{ $link }}';
        $la = $link;
        if(is_array($link)){
        $con = preg_replace_callback('/\{\{\s*\$link\[(.*?)\]\s*\}\}/', function ($matches) use ($link) {
            $key = trim($matches[1], "'\" ");
            return $link[$key] ?? '';
        }, $con);
        $l = $la = "daslsdkösdkdkl";

    }

            $con = str_replace([$l,'{{ $nick }}','{{ $title }}'],[$la,$nick,$title],$con);


        return nl2br($con);
    }
    function SendMail($title,$template,$email,$nick,$link,$html)
    {
        $nick = trim($nick);
        \Log::info([$template,$email,$nick,$link]);
        // Mail::to($email)->send(new GeneralMail($title,$link,$nick,$html,$template));
        Mail::send([], [], function ($message) use ($email, $html, $title) {
            $message->to($email)
                ->subject($title)
                 ->html($html); // <-- das ist entscheidend!
        });
    }
    function randomString64() {
        $chars = '-_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $length = strlen($chars);
        $result = '';

        for ($i = 0; $i < 64; $i++) {
            $result .= $chars[random_int(0, $length - 1)];
        }

        return $result;
    }
    function Quote($mail)
    {
        return str_replace([" ","@","."],["_","_at_","_dot_"],$mail);
    }
}
