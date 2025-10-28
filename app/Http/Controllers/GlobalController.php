<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class GlobalController extends Controller
{
    public function __construct()
    {
        if (Schema::hasColumn('users', 'uhash')) {


        $i64 = DB::table('users')
            ->where('uhash', '')
            ->orWhere("uhash", NULL)
            ->select('id')
            ->get();

        foreach ($i64 as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['uhash' => $this->randomString64()]);
        }
        }

        if (Schema::hasTable('contacts') && Schema::hasColumn('contacts', 'uhash')) {

            $i65 = DB::table("contacts")
                ->where('uhash','')
                ->select('id')
                ->get();
        foreach($i65 as $cont)
        {
            DB::table('contacts')
                ->where('id', $cont->id)
                ->update(['uhash' => $this->randomString64()]);
        }
        }

    }
    public function PasswordPrint(Request $request){
        echo Hash::make($request->pw);
    }
    public function PasswordForm(){
        echo "<form method='post' action='/tables/pwprint'>
                <input type='hidden' name='_token' id='token' value='" . csrf_token() . "' />
                <input type='password' name='pw'>
                <input type='submit' name='save' value='Anzeigen'>
            </form>";
    }
    public static function SetDomain()
    {
        $subdomain = SD(); // Subdomain extrahieren
    //     \Log::info("sd: ".$subdomain);

        if (SD() === 'mfx') {
            config(['database.default' => 'mariadb_mfx']);
        }
        elseif(SD() === "dag"){
            config(['database.default' => 'mariadb_dag']);
        }
        else {
            config(['database.default' => 'mariadb']);
        }
        //     if ($subdomain == 'mfx') {
    //         // Verbindung zu 'laravel-tutorial-hm' wechseln
    //         \DB::setDefaultConnection('mariadb_mfx');
    //     } else {
    //         // Standardverbindung zu 'laravel-tutorial'
    //         \DB::setDefaultConnection('mariadb');
    //     }

    }
    // public static function SetDomain()
    // {
    //     $host = request()->getHost(); // z. B. "mfx.localhost.de"
    //     $subdomain = explode('.', $host)[0];

    //     // \Log::info("Subdomain erkannt03: $subdomain");

    //     $tenant = \App\Models\Tenant::where('subdomain', $subdomain)->first();

    //     if ($tenant) {
    //         //\Log::info("Tenant gefunden: {$tenant->database} 01");
    //         if($subdomain == "ab")
    //         {
    //             $dba = "mariadb";
    //         }
    //         else
    //         {
    //             $dba = "mariadb_".$subdomain;
    //         }
    //         // Datenbankwechsel durchführen
    //         DB::connection()->setDatabaseName($dba);
    //         config(['database.connections.mysql.database' => $tenant->database]);
    //         \DB::purge($dba); // Cache der alten Verbindung löschen
    //         \DB::reconnect($dba); // Neue Verbindung aufbauen

    //         app()->instance('tenant', $tenant); // Globale Verfügbarkeit
    //     }
    //     else{
    //         \Log::info("KEIN TENANT");
    //     }
    // }
        public static function SD($pn=''){


            $subb = explode('.', str_replace("www.",'',request()->getHost()))[0];
//             \Log::info($subb);
            $pm = ["ab"=>"Asarios Blog",
                   "dag"=>"Monika Dargies",
                    "mfx"=>"MarbleFX",
                    "mjs"=>"Mitja Schult",
                    "chh"=>"Rechtsanwalt Christian Henning"];
            switch($subb){
                case "asario":
                    $subb = "ab";

                break;
                case "monikadargies":
                    $subb = "dag";

                break;
                case "marblefx":
                    $subb = "mfx";

                break;
                case "mjs":
                    $subb = "mjs";

                break;
                case "ra-c-henning":
                    $subb = "chh";

                break;
                default:
                $subb = $subb;
                break;
            }
            if( substr_count(@$_SERVER['REQUEST_URI'],"/admin/"))
            {
             ///   $subb  = "ab";
                // dd($subb);
            }
            if(empty($subb))
            {
                $subb = "ab";
            }

            if(!$pn){
                return $subb;
            }
            return $pm[$subb];
        // Den Host in Teile aufteilen (subdomain.localhost.de -> ['subdomain', 'localhost', 'de'])


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
    }


