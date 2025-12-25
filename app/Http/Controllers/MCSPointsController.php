<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class MCSPointsController extends Controller
{
    //
    var $ccnt;
    var $scnt;
    var $ncnt;

    function __construct()
    {
        // Comments
        $this->ccnt = 5;
        // Stars/Rating
        $this->scnt = 1;
        // Newsletter read
        $this->ncnt = 8;
    }


    function GetCommentsCount($users_id){

        $cnt = DB::table("comments")->where("users_id",$users_id)->count();

        if ($users_id === null) {
            $cnt = 0;
        }
        return $cnt;
    }
    function GetStarsCount($users_id)
    {
        $cnt = DB::table("ratings")->where("users_id",$users_id)->count();
        if ($users_id === null) {
            $cnt = 0;
        }
        return $cnt;
    }
    function GetNewslCount($users_id)
    {
        //$cnt = DB::table("newsl_succ")->where("users_id",$users_id)->count();
        if ($users_id === null) {
            $cnt = 0;
        }
        return @$cnt ?? 0;
    }
    function GetCount(Request $request)
    {
        if(!Schema::hasTable("comments")){
            return;
        }
        $users_id = $request->users_id;
        if(empty($users_id))
        {
            $users_id = Auth::id();
        }

        $cnt = $this->GetCommentsCount($users_id)*$this->ccnt;
        $cnt = $cnt + $this->GetStarsCount($users_id)*$this->scnt;
        //$cnt = $cnt + $this->GetNewslCount($users_id)*$this->ncnt;
        // dd($cnt);

        return $cnt;
    }
}
