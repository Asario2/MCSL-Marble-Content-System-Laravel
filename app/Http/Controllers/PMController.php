<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pm_index()
    {
        //
        $inbox = DB::table('private_messages')
        ->leftJoin('private_messages_text', 'private_messages.private_messages_text_id', '=', 'private_messages_text.id')
        ->leftJoin('users', 'private_messages.users_id', '=', 'users.id')
        ->where('private_messages.to_id', Auth::id())
        ->where('private_messages.xis_disabled', '0')
        ->where("private_messages.public","1")
        ->select('private_messages.subject','private_messages.id as id',"users.first_name","users.id AS UID","users.website","private_messages.public","users.last_login_at as lastlogin","private_messages.to_id", 'private_messages.checked',"users.birthday",'private_messages_text.message as message',"private_messages.created_at as created_at","users_id as users_id",'users.name as user',"users.profile_photo_path as avatar")
        ->orderBy("private_messages.created_at","DESC")
        ->get();

        $inbox = $inbox->map(function ($msg) {
            $msg->subject = decval($msg->subject);
            $msg->message = decval($msg->message);
                if (!empty($msg->birthday)) {
                $msg->age = Carbon::parse($msg->birthday)->age;
            } else {
                $msg->age = null;
            }
            return $msg;
        });
        $outbox = DB::table('private_messages')
        ->leftJoin('private_messages_text', 'private_messages.private_messages_text_id', '=', 'private_messages_text.id')
        ->leftJoin('users', 'private_messages.to_id', '=', 'users.id')
        ->where('private_messages.users_id', Auth::id())
        ->where('private_messages.xis_disabled', '0')
        ->where("private_messages.public","2")
        ->select('private_messages.subject','private_messages.id as id',"users_id as users_id","users.first_name","users.last_login_at as lastlogin","private_messages.public","private_messages.to_id","users.birthday",'private_messages_text.message as message',"private_messages.created_at as created_at",'users.name as user',"users.profile_photo_path as avatar")
        ->orderBy("private_messages.created_at","DESC")
        ->get();

        $outbox = $outbox->map(function ($msg) {
            $msg->subject = decval($msg->subject);
            $msg->message = decval($msg->message);
            if (!empty($msg->birthday)) {
                $msg->age = Carbon::parse($msg->birthday)->age;
            } else {
                $msg->age = null;
            }
            return $msg;
        });
        $form = DB::table("users_config")->where("users_id",Auth::id())->get();
    return Inertia::render('Homepage/ab/PM',["inboxArr" => $inbox,"outboxArr"=>$outbox,"form"=>$form]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if(!Auth::id()){
            header("Location: /login");
            exit;
        }
        $lastinsertid = DB::table("private_messages_text")
        ->insertGetId([
            'message' => encval($this->rembr($request->message)),
        ]);

        $uid = $request->uid ?? Auth::id();

        $ts = NOW();
        // \Log::info(["subject"=>$request->subject,"private_message_texts_id"=>$lastinsertid,"to_id"=>$request->to_id,"users_id"=>Auth::id(),"public"=>"1"]);
        // \Log::info(["subject"=>$request->subject,"private_message_texts_id"=>$lastinsertid,"to_id"=>$request->to_id,"users_id"=>Auth::id(),"public"=>"2"]);
        DB::table("private_messages")->insert(["created_at"=>$ts,"subject"=>encval($request->subject),"private_messages_text_id"=>$lastinsertid,"to_id"=>$request->to_id,"users_id"=>$uid,"public"=>"1"]);
        DB::table("private_messages")->insert(["created_at"=>$ts,"subject"=>encval($request->subject),"private_messages_text_id"=>$lastinsertid,"to_id"=>$request->to_id,"users_id"=>$uid,"public"=>"2"]);


    }

    /**
     * Display the specified resource.
     */
    public function rembr(string $text)
    {
        //
        $text = str_replace(["<br />","<br>","<br/>","<br >"],"\n",$text);
        $content = ltrim($text);
        $text = preg_replace('/^(<br\s*\/?>)+/i', '', $content);

        $text = str_replace(str_repeat("<br />\n",3),"<br />",nl2br($text));
        $text = str_replace(
            ["\r", "\n", "\t"],
            ["\\r", "\\n", "\\t"],
            $text
        );
        return str_replace(str_repeat("<br>\n",6),'',$text);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        DB::table("private_messages")->where("id",$id)->update(["checked"=>"1","xis_checked"=>"1"]);
        return true;
    }
    public function update_more(Request $request)
    {
        //
         $ids = explode(",",$request->ids);
         foreach($ids as $id){
            DB::table("private_messages")->where("id",$id)->update(["checked"=>"1","xis_checked"=>"1"]);
         }

        return true;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // if(!CheckRights(Auth::id(),'private_messages',"delete")){
        //     header("Location: /no-rights");
        //     exit;
        // }
        $id = $request->id;
        $iid = DB::table("private_messages")->where("id",$id)->value("private_messages_text_id AS iid");

        $res2 = DB::table("private_messages")->where('private_messages_text_id',$iid)->count();
        $res = DB::table("private_messages")->where('id', $id)->delete();
        if(@$res2 < 2){
//             \Log::info("IID:".$iid);
            DB::table("private_messages_text")->where("id",$iid)->delete();
        }




        return response()->json([
                'status' => 'success',
                'message' => 'Eintrag erfolgreich gelöscht',

            ]);


    }
    public function destroy_more(Request $request)
    {
        // if(!CheckRights(Auth::id(),'private_messages',"delete")){
        //     header("Location: /no-rights");
        //     exit;
        // }
        $ids = explode(",",$request->ids);
//         \Log::info(["IDS:"=>$ids]);
        foreach($ids as $id){

        $iid = DB::table("private_messages")->where("id",$id)->value("private_messages_text_id AS iid");

        $res2 = DB::table("private_messages")->where('private_messages_text_id',$iid)->count();
        $res = DB::table("private_messages")->where('id', $id)->delete();
        if(@$res2 < 2){
//             \Log::info("IID:".$iid);
            DB::table("private_messages_text")->where("id",$iid)->delete();
        }
        }




        return response()->json([
                'status' => 'success',
                'message' => 'Einträge erfolgreich gelöscht',

            ]);


    }
}

