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
        ]);

        $rating = $request->rating;
        $postId = $request->postId;
        $table = $request->table;

        $rating = $validated['rating'];
        $postId = $validated['postId'];
        $table = $validated["table"];


            $userId = Auth::id();
            $existingRate = $this->GetExistingRate($table,$postId);
            $id = is_object($existingRate) ? $existingRate->id : $existingRate;
            if(!$id)
            {
                DB::table("ratings")->insert(
                    [
                        "table" => $table,
                        "rating" => $rating,
                        "images_id" => $postId,
                        "users_id" => $userId,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ]);
            }
            else{
                DB::table("ratings")->where("id",$id)->update(
                    [
                        "rating" => $request->rating,
                        "updated_at" => now(),
                    ]
                    );

                }




            return response()->json(['status' => 'success']);


        // Speichern der Bewertung, z.B. in einer Rating-Datenbank-Tabelle
        // Rating::create([...]);
        // $existingRate = $this->GetExistingRate($table, $postId);
        //     $id = is_object($existingRate) ? $existingRate->id : $existingRate;
        // if(!$id)
        // {
        //     DB::table("ratings")
        //     ->insert(["image_id"=>$postId,
        //     "rating"=>$rating,
        //     "table"=>$table,
        //     "users_id"=>Auth::id(),
        //     "created_at"=>now(),
        //     "updated_at"=>now()]);
        // }
        // else{

        //     DB::table("ratings")->where("id",$id)
        //     ->update(["image_id"=>$postId,
        //     "rating"=>$rating,
        //     "table"=>$table,
        //     "users_id"=>Auth::id(),
        //     "updated_at"=>now()]);
        // }

        return response()->json(['status' => 'success']);
    }
    public function GetExistingRate($table,$postId)
    {
        $id = DB::table("ratings")->where("table",$table)->where("users_id",Auth::id())->where("images_id",$postId)->select("id")->first();
        $id = !$id ? null : $id;
        return $id;
    }
    public function getRat(Request $request)
    {
        $ex = DB::table("ratings")
            ->where("images_id",$request->postId)
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
