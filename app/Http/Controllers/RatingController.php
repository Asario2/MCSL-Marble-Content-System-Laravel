<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $rating = $request->input('rating');
        $postId = $request->input('postId');
        $table = $request->input("table");

        // Bewertung speichern, z.B. in einer Datenbank
        // Beispiel: Post::find($postId)->ratings()->create(['rating' => $rating]);
        //

        return response()->json(['status' => 'success']);
    }
    public function getStars($table, $postId)
    {
        $userId = Auth::id();
        $rating = DB::table("ratings")
            ->where("table", $table)
            ->where("images_id", $postId)
            ->where("users_id", $userId)
            ->value("rating"); // Nur die Bewertung holen

        return response()->json(['rating' => $rating ?? 0]); // Falls kein Wert existiert, 0 zurückgeben
    }
    public function saveRating(Request $request)
    {
        // Beispielhafte Validierung und Speicherung
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'postId' => 'required|integer',
                'table' => 'required|string',
                "email" => 'nullable|string',
                'password'=> 'nullable|string|min:6'

            ]);

        $rating = $request->rating;
        $postId = $request->postId;
        $table = $request->table;

        $rating = $validated['rating'];
        $postId = $validated['postId'];
        $table = $validated["table"];
        $email = $validated["email"] ?? Auth::user()->email;
        \Log::info($validated);

            $userId = Auth::id() ?? '7';
            if($userId != "7")
            {
                $email = DB::table("users")->where("id",$userId)->value("email");
            }

            $nick_exists = DB::table("users")->where('name',$validated['email'])->orWhere("email",$validated['email'])->exists();
            if($userId == "7" && $nick_exists)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nickname bereits vorhanden, bitte Passwort ausfüllen oder einloggen',
                ]);
            }

            $existingRate = $this->GetExistingRate($table,$postId,$email,$userId);
            $id = is_object($existingRate) ? $existingRate->id : $existingRate;
            \Log::info("ID: ".$id);
            $duration = 30000;
            if(!$id && $userId)
            {
                DB::table("ratings")->insert(
                    [
                        "table" => $table,
                        "rating" => $rating,
                        "images_id" => $postId,
                        "users_id" => $userId,
                        "email"=> $email,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ]);
                    if($userId != "7")
                    {
                        $status = 'points';
                        $message = 'Du hast 1 MCSL Point gesammelt';
                    }
                    else
                    {
                        $status = "success";
                        $message = "Deine Bewertung wurde gespeichert";
                    }

            }
            else{
                DB::table("ratings")->where("id",$id)->update(
                    [
                        "rating" => $request->rating,
                        "updated_at" => now(),
                    ]
                    );
                    $status = "info";
                    $message = "Wir haben deine Punkte aktualisiert";
                }




            return response()->json(['status' => $status, "message"=>$message,"duration"=>$duration]);



        return response()->json(['status' => 'success']);
    }
    public function GetExistingRate($table,$postId,$email,$users_id)
    {
        // $uss = DB::table("ratings")
        //     ->leftJoin("users", "ratings.users_id", "=", "users.id")
        //     ->select("users.email", "ratings.users_id")
        //     ->get();

        // foreach ($uss as $val) {
        //     DB::table("ratings")
        //         ->where("users_id", $val->users_id)
        //         ->update([
        //             "email" => $val->email
        //         ]);
        // }
        $id = DB::table("ratings")->where("table",$table)->where("images_id",$postId)->where("users_id",$users_id)->where("email",$email)->select("id")->first();
        \Log::info("idnew:".json_encode([$table,Auth::id(),$postId,$email]));
        $id = !$id ? null : $id;
        return $id;
    }
    public function getRat(Request $request)
    {
        $ex = DB::table("ratings")
            ->where("images_id",$request->postId)
            ->where("table",$request->table)
            ->selectRaw('AVG(rating) as average, COUNT(id) as total')
            ->first();
            return response()->json($ex);
    }
    public function getAverageRating($table, $postId)
    {
        $result = DB::table('ratings')
            ->where('table', $table)
            ->where('images_id', $postId)
            ->selectRaw('AVG(rating) as average, COUNT(id) as total')
            ->first();

        return response()->json([
            'average' => $result->average ? round($result->average, 1) : 0,
            'total' => $result->total ?? 0
        ]);
    }
    public static function getTotalRating($table)
    {
        $url = $_SERVER['REQUEST_URI'];
        $table = basename(parse_url(request()->fullUrl(), PHP_URL_PATH));
        $rating = DB::table('ratings')
            ->where('table', $table)
            ->groupBy('images_id')
            ->selectRaw('images_id, AVG(rating) as average, COUNT(id) as total')
            ->get()
            ->keyBy('images_id');
        $queries = DB::getQueryLog();

        // \Log::info(json_encode([$rating,$queries,$table]));
        return response()->json($rating);
    }
    public static function pb(){
        buildPrivacyText();
    }
}
