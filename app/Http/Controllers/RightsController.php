<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\AdminTable;
use App\Models\UsersRight;
use App\Models\User;


class RightsController extends Controller
{
    /**
     * Gibt den Wert eines bestimmten Rechts für eine bestimmte Tabelle und einen bestimmten Benutzer zurück.
     *
     * @param string $table Tabellenname (z. B. 'posts')
     * @param string $right Rechte-Spaltenname (z. B. 'view_table')
     * @param int $user_id ID des Benutzers
     * @return \Illuminate\Http\JsonResponse
     */
    public  function hasCreated($table)
    {
        $exists = Schema::hasTable($table) && Schema::hasColumn($table, 'created_at');
        return response()->json($exists);
    }
    public function getUserRights(Request $request,$table,$right)
    {
        if(!Auth::check())
        {
            //return response()->json(0);
            $userId = 1;
        }
        // Stelle sicher, dass der Benutzer eingeloggt ist
        else{
            $userId = Auth::id();
        }

        // \Log::info("uid: ".json_encode($userId) );
        $columns = Schema::getColumnListing("users_rights");
        // Wenn der Benutzer nicht eingeloggt ist, gib einen Fehler zurück
        $tableid = DB::table("admin_table")->where("name",$table)->pluck("position")->first();
        if (!$userId || !$tableid || !in_array($right."_table",$columns)) {
            return response()->json(0);
        }

        $rightfe = DB::table("users")
        ->where("users.id", $userId)
        ->leftJoin("users_rights", "users.users_rights_id", "=", "users_rights.id")
        ->select("users_rights." . $right . "_table")
        ->first();

    if (!$rightfe) {
        return response()->json(['error' => 'Rechte nicht gefunden'], 404);
    }

    // Extrahiere den String-Wert (z. B. "0110101")
    $rightString = $rightfe->{$right . '_table'};

    // Prüfe, ob der Index existiert
    if (!isset($rightString[$tableid])) {
        return response()->json(['error' => 'Ungültiger Tabellen-Index'], 400);
    }

    // Rückgabe des einzelnen Bits
    return response()->json(intval($rightString[$tableid]));
}
public function GetRights($table,$right){
    if (!Auth::check()) {
return false;
} else {
    $userId = Auth::id();
}

$rightfe = DB::table("users")
    ->where("users.id", $userId)
    ->leftJoin("users_rights", "users.users_rights_id", "=", "users_rights.id")
    ->select("users_rights.".$right."_table as posi")
    ->first();

$pos = DB::table("admin_table")
    ->where("name", $table)
    ->value("position");

// RICHTIG: Position nutzen!
$rf = substr(@$rightfe->posi, ($pos), 1);
if(!$pos && $pos !== "0"){
$rf = "0";
}
\Log::info("R:".$right."POSS:".($pos-1)."V:".$rf);
    return response()->json($rf);
}
public function allTableRights($right)
{
    // Beispiel: $right = "view_table"
    $bin = auth()->user()->rights->$right; // z.B. "1011"

    // Hole alle Tabellen aus admin_tables
    $tables = \DB::table('admin_table')->orderBy("name","ASC")->pluck('name')->toArray();

    $array = [];

    foreach ($tables as $i => $name) {
        $array[$name] = substr($bin, $i, 1) ?: "0";
    }

    return $array;
}
public function GetRights_old()
{
    // Angenommen, der eingeloggte User hat eine `users_rights_id` oder ähnliches
    $user = Auth::user();

    if (!$user || !$user->users_rights_id) {
        return response()->json(['error' => 'Keine Rechte gefunden'], 403);
    }

    $rights = UsersRights::find($user->users_rights_id);

    if (!$rights) {
        return response()->json(['error' => 'Rechtegruppe nicht gefunden'], 404);
    }

    // Gib nur die relevanten Felder zurück
    return response()->json([
        'view_table'         => $rights->view_table,
        'add_table'          => $rights->add_table,
        'edit_table'         => $rights->edit_table,
        'publish_table'      => $rights->publish_table,
        'date_table'         => $rights->date_table,
        'delete_table'       => $rights->delete_table,

    ]);
}
function GetTables_posi()
{
    $tables = DB::table("admin_table")->select("name","position")->get();
    return response()->json(["at",$tables]);
}
function GetRights_all()
{
    if(!Auth::check())
    {
        //return response()->json(0);
        $userId = 0;
    }
    // Stelle sicher, dass der Benutzer eingeloggt ist
    else{
        $userId = Auth::id();
    }
    $rightfe = DB::table("users")
    ->where("users.id", $userId)
    ->leftJoin("users_rights", "users.users_rights_id", "=", "users_rights.id")
    ->select("users_rights.view_table","users_rights.add_table","users_rights.edit_table","users_rights.publish_table","users_rights.date_table","users_rights.delete_table")
    ->first();
    // \Log::info("RFE:".json_encode($rightfe));
    return response()->json(["userRights",$rightfe]);
}
}
