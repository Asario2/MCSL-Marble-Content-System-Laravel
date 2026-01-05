<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\EncryptController;
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
        $exists = DB::table("newsletter_reci")->where("email",$request->email)->exists();
        if(!$exists){


        DB::table("newsletter_reci")->insert([
            "pub"=>0,
            "uhash"=> $uhash,
            "email"=>$request->email,
            "title"=>$request->title,
            "prename"=>$request->firstname,
            "surname"=>$request->lastname,
            "comphash"=>bin2hex(random_bytes(32)),
        ]);
        }
        else{
            DB::table("newsletter_reci")->where("email",$request->email)->update([
            "title"=>$request->title,
            "prename"=>$request->firstname,
            "surname"=>$request->lastname,
            ]);
            $uhash = DB::table("newsletter_reci")->where("email",$request->email)->value("uhash");
        }
        $title = "Newsletter Anmeldung";
        $link = "http://".request()->getHost()."/mail/subscribe/".$uhash."/".$request->email;
        $nick = trim($request->title." ".$request->firstname." ".$request->lastname) ?? "Liebe Leserin, lieber Leser,";
        $content_alt = '';
        $template = '';
        $signatur = '';
            $html = html_entity_decode(
        View::file(
            resource_path('views/emails/newslsub.blade.php'),
            compact('title', 'link', 'nick', 'content_alt', 'template', 'signatur'))->render()
    );
        $this->SendMail("Newsletter Anmeldung","emails.newslsub",$request->email,$request->title." ".$request->firstname." ".$request->lastname,"http://".request()->getHost()."/mail/subscribe/".$uhash."/".$request->email,$html,$uhash);
    }

function SendMail($title, $template, $email, $nick, $link, $html, $uhash = '',$comp='') {
    if(empty($email)) return false;

    $html = str_replace('%uhash%', $uhash, $html);
    $html = str_replace("%comp%",$comp,$html);
    $email = $email;
    Mail::send([], [], function ($message) use ($email, $html, $title) {
        $message->to($email)
                ->subject($title)
                ->html($html);
    });
}
    function SendMail_old($title,$template,$email,$nick,$link,$html,$uhash='')
    {
        $nick = trim($nick);
//         \Log::info([$template,$email,$nick,$link]);
        if(empty($email)){
            return false;
        }
        $email = $email;
        $html = str_replace('%uhash%',@$uhash,$html);
//         \Log::info($uhash);
        // Mail::to($email)->send(new GeneralMail($title,$link,$nick,$html,$template));
        Mail::send([], [], function ($message) use ($email, $html, $title,$uhash) {
            $message->to($email)
                ->subject($title)
                 ->html($html); // <-- das ist entscheidend!
        });
    }
    public function getEmailByUhash(string $uhash): ?string
    {
        // 1. Prüfe Users
        $user = DB::table('users')
            ->where('uhash', $uhash)
            ->select('email')
            ->first();

        if ($user && !empty($user->email)) {
            return $user->email;
        }

        // 2. Prüfe Contacts
        $contact = DB::table('contacts')
            ->where('uhash', $uhash)
            ->select('email')
            ->first();

        if ($contact && !empty($contact->email)) {
            return $contact->email;
        }

        // 3. Prüfe Newsletter_Recei
        $newsletter = DB::table('newsletter_reci')
            ->where('uhash', $uhash)
            ->select('email')
            ->first();

        if ($newsletter && !empty($newsletter->email)) {
            return $newsletter->email;
        }

        // Wenn nichts gefunden wurde
        return null;
    }
    public function UnSubscribe_Newsl($uhash)
    {
        $email = $this->getEmailByUhash($uhash);
        $email = $email;
        if($email){
            // $resul = DB::table("newsletter_blacklist")->updateOrInsert(["mail"=>$email,"created_at"=>now(),"pub"=>"1"]);
       $exists = DB::table('newsletter_blacklist')
            ->where('uhash', $uhash)
            ->exists();

        if (!$exists) {
            DB::table('newsletter_blacklist')->insert([
                'mail' => $email,
                'pub' => 1,          // 👈 nur beim Insert
                'created_at' => now(),
                "uhash" => $uhash,

            ]);
        } else {
            DB::table('newsletter_blacklist')->update([
                'updated_at' => now(),
            ]);
        }
        if($uhash){
            // DB::table("newsletter_reci")->where("uhash",$uhash)->delete();
        }
        }
        return Inertia::render("Components/Social/Newsl_Blacklist");
    }

    //return $ma->PrevMail("[MCSl] Newsletter","emails.newsletter",$email,$nick,$link,$content,$signatur);
    function PrevMail(Request $request,$title,$template,$email,$nick,$link,$content,$signatur,$uhash,$chash){

    $chash = DB::table("newsletter")->where("id",$request->mailbodyId)->value("comphash");
    $footer = $signatur;
        // dd($request->all());



    $signatur = $this->replink($signatur,$title,$email,$nick,$link,@$chash);



    $content  = $this->replink($content,$title,$email,$nick,$link,@$chash);

    $signatur2 = $signatur.$this->subm_btn();
        $content_alt = $content.$signatur;
    $data = [$title,$link,$nick,$content,$template,$footer];
    $html = rumLaut(html_entity_decode((View::file(resource_path('views/vendor/mail/html/preview.blade.php'), compact('title','link','nick','content','template','signatur2'))->render())));

    $html2 = rumLaut(html_entity_decode(
        View::file(
            resource_path('views/vendor/mail/html/send.blade.php'),
            compact('title', 'link', 'nick', 'content_alt', 'template', 'signatur'))->render()
    ));
    // $html2 = str_replace("%uhash%",$uhash,$html2);
    session(['signatur' => $signatur,"reci"=>$request->recipients, "content"=>html_entity_decode($html2), "title"=>$title,"email"=>$email,"nick"=>$nick,"template"=>$template]);
    return $html;


    // HTML rendern
    // $html = View::make('email',$data)->render();

    // return response()->json(['html' => $html]);

//        return View::make('vendor.notifications.email',);
    }
    function subm_btn()
    {
        return "<br /><br /><a class='button-primary' href='/email/send/'>E-mail Senden</a>";
    }
    function send_newsletter(Request $request){
        $i = 0;
        $sendm = [];
        $entries = explode(", ",session("reci"));
        $groups = [];
        $ugroups = [];
        $contacts = [];
        $users = [];
        // $to = DB::table("newsletter")
        // dd($request->all());


        foreach($entries as $key=>$val){
            if(substr_count($val,"{")){
                $groups[] = $val;
            }
            elseif(substr_count($val,"(")){
                $contacts[] = $val;
            }
            else{
                $users[] = $val;
            }
        }
        foreach ($groups as $reg){
            $ug = str_replace(['{', '}'], '', $reg);

            $ugroups = DB::table('users')
            ->join('users_rights', 'users.users_rights_id', '=', 'users_rights.id')
            ->leftJoin('newsletter_blacklist', 'newsletter_blacklist.mail', '=', 'users.email')
            ->where('users_rights.name', $ug)
            ->whereNull('newsletter_blacklist.mail') // => Nur wenn NICHT in Blacklist
            ->select('users.email', 'users.uhash', 'users.name')
            ->get();

        }
        foreach ($ugroups as $regro)
        {
            $uhash = @$regro->uhash;
            $email = @$regro->email;
            $nick = @$regro->name;
            $sendm[] = $email;
            $this->SendMail(session('title'),session('template'),$email,$nick,'',html_entity_decode(session('content')),$uhash);
            $i++;
        }
        foreach($contacts as $con)
        {
            preg_match_all('/\(([^)]+)\)/', $con, $matches);
            $email = implode("",$matches[1]);

            $nick = $email;

            $res_alt = DB::table('contacts')->where('email_hash', hash('sha256', $email))->select("uhash","email")->first();

            $uhash = $res_alt->uhash;
            $email = $res_alt->email;
            if(!in_array($email,$sendm) && !empty($email)){
                $this->SendMail(session('title'),session('template'),$email,$nick,'',html_entity_decode(session('content')),$uhash);
                $i++;
                $sendm[] = $email;
            }

        }
        foreach ($users as $rek)
        {
            if(!CheckZRights("SendMailToAll")){
            $res = DB::table("users")->leftJoin("users_config as uc","users.id","=","uc.users_id")->where("name",$rek)->where('uc.xch_newsletter', 'LIKE', '%to_mail%')->select("email","uhash","name")->first();
            }
            else{
            $res = DB::table("users")->where("name",$rek)->select("email","uhash","name")->first();
            }
            // dd(session('reci'));
            $uhash = @$res->uhash;
            $email = @$res->email;
            $nick = @$res->name;
            // $comp = $co->comphash;
            if(!in_array($email,$sendm) && !empty($res) && !empty($email)){
                $this->SendMail(session('title'),session('template'),$email,$nick,'',html_entity_decode(session('content')),$uhash);
                $i++;
                $sendm[] = $email;
            }

        }
        // dd($entries);
        if(in_array("_Externe Empfänger_,",$entries))
        {
            $resi = DB::table("newsletter_reci")->where("xis_checked","1")->get();
            foreach($resi as $rep)
            {
                $uhash = $rep->uhash;
                $email = $rep->email;
                $ni = $rep->surname." ".$rep->prename;
                $nick = trim($ni) ?? $email;
                $this->SendMail(session('title'),session('template'),$email,$nick,'',html_entity_decode(session('content')),$uhash);
                $i++;
                $sendm[] = $email;
            }
        }





       return Inertia::render("Components/Social/Emails_Sended",["i"=>$i]);

    }
    function SendReg(Request $request) {
        $email = "parie@gmx.de";
        $nick  = htmlspecialchars($request->name, ENT_QUOTES, 'UTF-8');
        $link  = route('users.show', $request->id); // Beispiel-Link

        $cont = '
            <h2>Hallo Paule,</h2>
            <p>Ein neuer Benutzer namens ' . $nick . ' hat sich auf ' . request()->getHost() . ' registriert.<br><br></p>
            <p><a href="' . $link . '">Zum Profil</a></p>
        ';

        $uhash = "asd";

        $this->SendMail("Neuer Nutzer auf " . SD(1), "send", $email, $nick, $link, $cont, $uhash);
    }
    function replink($con,$title,$email,$nick,$link='',$chash=''){
        $l = '{{ $link }}';
        $la = $link;
        // dd($link);
        if(is_array($link)){
        $con = preg_replace_callback('/\{\{\s*\$link\[(.*?)\]\s*\}\}/', function ($matches) use ($link) {
            $key = trim($matches[1], "'\" ");
            return $link[$key] ?? '';
        }, $con);
        $l = $la = "daslsdkösdkdkl";

    }

            $con = str_replace("%chash%",@$chash,$con);
            $con = str_replace([$l,'{{ $nick }}','{{ $title }}'],[$la,$nick,$title],$con);


        return nl2br($con);
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
    public function saveMail(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'Body' => 'required|string',
            "name" => 'required|string',
            'signatur_id' => 'nullable|integer',
        ]);

        DB::table('newsletter')->insert([
            'subject' => $validated['subject'],
            'body' => $validated['Body'],
            'name' => $validated['name'],
            'signatur_id' => $validated['signatur_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('newsletter')->update([
            'position' => DB::raw('position + 1')
        ]);
        return back()->with('success', 'E-Mail gespeichert.');
    }

    public function saveSignature(Request $request)
{

    $validated = $request->validate([
        'signatur_id' => 'nullable|integer',
        'signature_text' => 'required|string',
        'signature_name' => 'nullable|string',
    ]);

    try {
        if (!empty($validated['signatur_id']) && @$asd == "23131") {
            // ✅ Bestehende Signatur aktualisieren
            DB::table('signatur')
                ->where('id', $validated['signatur_id'])
                ->update([
                    'sigtext' => $validated['signature_text'],
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Signatur erfolgreich aktualisiert',
                'id' => $validated['signatur_id']
            ]);
        } else {
            // ✅ Neue Signatur speichern
            $signatureId = DB::table('signatur')->insertGetId([
                'name' => $validated['signature_name'] ?? 'Neue Signatur',
                'sigtext' => $validated['signature_text'],
                'position' => 0, // optional
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('signatur')->update([
            'position' => DB::raw('position + 1')
        ]);
            return response()->json([
                'success' => true,
                'message' => '✅ Signatur erfolgreich gespeichert',
                'id' => $signatureId
            ]);
        }
    } catch (\Exception $e) {
        // ❌ Fehlerbehandlung
        return response()->json([
            'success' => false,
            'message' => 'Fehler beim Speichern der Signatur: ' . $e->getMessage(),
        ], 500);
    }
}


}
