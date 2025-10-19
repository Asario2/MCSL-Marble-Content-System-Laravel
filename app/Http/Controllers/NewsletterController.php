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


    }
    public function RegisterMail(Request $request)
    {
        $sdr = DB::table("newsletter_reci")->pluck("userhash")->where("email",$request['email'])->first();
        if($sdr['userhash'] == $request['uhash'])
        {
            DB::table('newsletter_reci')->insert([
                'email' => $request['email'],
                'firstname'  => $request['firstname'],
                "lastname" =>$request['lastname'],
                "title"=> $request['title'],
                'subscribed_at' => now(),
            ]);
            return true;
        }
        else
        {
            return false;
        }

    }
}
