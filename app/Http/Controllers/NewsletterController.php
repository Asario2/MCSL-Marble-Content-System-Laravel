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
use App\Http\Controllers\MailController;
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
        $sdr = DB::table("newsletter_reci")->pluck("uhash")->where("email",encval($request['email']))->first();
        if($sdr['uhash'] == $request['uhash'])
        {
            DB::table('newsletter_reci')->insert([
                'email' => encval($request['email']),
                'firstname'  => encval($request['firstname']),
                "lastname" => encval($request['lastname']),
                "title"=> encval($request['title']),
                'subscribed_at' => now(),
                "uhash"=>$request['uhash'],
            ]);
            DB::table("newsletter_blacklist")->where("uhash",$request['uhash'])->delete();

$adminEmail = "parie@gmx.de";  // oder deine IONOS-Mail
    $admin = (object) ['email' => $adminEmail]; // Dummy-Notifiable
    $admin->notify(new NewUserRegistered($user));

            return true;
        }
        else
        {
            return false;
        }

    }
}
