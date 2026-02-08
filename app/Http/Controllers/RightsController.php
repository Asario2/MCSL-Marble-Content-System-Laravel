<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\SQLUpdateController;
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
            $userId = 0;
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
public function remLog($id)
{
    if(!CheckZRights("Hlog"))
    {
        header("Location: /no-rights");
        exit;
    }
    try {
        DB::table("genxlo.xgen_hlog")->where("id", $id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Datensatz erfolgreich gelöscht',
        ]);

    } catch (\Exception $e) {
        \Log::error("Unable to delete HLog ID $id: " . $e->getMessage());

        return response()->json([
            'status'  => 'error',
            'message' => 'Datensatz konnte nicht gelöscht werden',
        ], 500);
    }


}
public function GetRights($table, $right)
{
    if (!Auth::check()) {
        return response()->json(0);
    }

    $userId = Auth::id();

    $rightfe = DB::table("users")
        ->where("users.id", $userId)
        ->leftJoin(
            "users_rights",
            "users.users_rights_id",
            "=",
            "users_rights.id"
        )
        ->select("users_rights.".$right."_table as posi")
        ->first();

    $pos = DB::table("admin_table")
        ->where("name", $table)
        ->value("position");

    if ($pos === null || !isset($rightfe->posi)) {
        return response()->json(0);
    }

    // Binärrecht auslesen
    $rf = substr($rightfe->posi, $pos, 1);

    return response()->json((int) $rf);
}

// public function AddFunction(Request $request)
// {

//     \Log::info("FUNC: ".$request->name);
// }
public function AddFunction(Request $request)
{
    if(!CheckZRights("UserRights"))
    {
       return redirect("/no-rights");
    }
    $new = $request->name;   // z.B. "neuer eintrag"
    $column = 'xkis_' .str_replace(' ', '_', $new);

    $table = 'users_rights';

    // 1) Alle Spalten aus users_rights holen
    $columns = DB::select("SHOW COLUMNS FROM `$table`");

    // Nur xkis_* herausfiltern
    $xkisColumns = array_map(fn($c) => $c->Field, array_filter($columns, function ($col) {
        return str_starts_with($col->Field, 'xkis_');
    }));

    // Alphabetisch sortieren
    sort($xkisColumns, SORT_NATURAL);
    \Log::info("cols:".json_encode($xkisColumns,JSON_PRETTY_PRINT)."col:".$column);
    // 2) Prüfen, ob Spalte schon existiert
    if (in_array($column, $xkisColumns)) {
        return response()->json(['message' => "Feld $column existiert bereits"], 400);
    }
    // Connection_GET
    $sqlu = NEW SQLUpdateController();
    $this->onl_con = $sqlu->GetDBCon(0,SD());


    // $this->addColumn($request);
    // 3) Alphabetische Position finden
    $insertAfter = null;
    foreach ($xkisColumns as $col) {
        if (strcmp($column, $col) > 0) {
            $insertAfter = $col; // letzter kleinerer Eintrag
        }
    }

    // 4) SQL erstellen: ALTER TABLE ... ADD COLUMN ... AFTER ...
    if ($insertAfter) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` TINYINT(1) NOT NULL DEFAULT 0 AFTER `$insertAfter`";
    } else {
        // Alphabetisch ganz am Anfang der xkis-Blöcke → vor dem ersten xkis_ Feld
        $first = $xkisColumns[0] ?? null;

        if ($first) {
            $sql = "ALTER TABLE `$table` ADD COLUMN `$column` TINYINT(1) NOT NULL DEFAULT 0 BEFORE `$first`";
        } else {
            // Es gibt noch keine xkis-Spalten → einfach ans Ende
            $sql = "ALTER TABLE `$table` ADD COLUMN `$column` TINYINT(1) NOT NULL DEFAULT 0";
        }
    }

    // 5) Spalte erzeugen
    DB::statement($sql);

    // DB::connection($this->onl_con)->statement($sql);


    // 6) UserRights ID 1 bekommt automatisch Wert = 1
    DB::table($table)->where('id', 1)->update([$column => 1]);
    DB::connection($this->onl_con)->table($table)->where('id', 1)->update([$column => 1]);
        return response()->json([
            'message' => "Feld <b>".str_replace("xkis_",'',$column)."</b> erfolgreich hinzugefügt",
            'insert_after' => $insertAfter,
            'new_column' => $column
        ]);
}
public function addColumn(Request $request)
{
    $table  = "users_rights";
    $column = $request->name;
    $desc   = $request->desc;

    // Key ohne xkis_
    $key = str_replace('xkis_', '', $column);

    // Datei-Pfad
    $path = app_path("/Models/Settings.php");

    if (!file_exists($path)) {
        return response()->json([
            'error' => "Settings.php nicht gefunden!",
        ], 500);
    }

    // Settings-Klasse laden
    require_once $path;
    $current = \App\Models\Settings::$exl;

    // Prüfen ob Key existiert
    if (array_key_exists($key, $current)) {
        return response()->json([
            'message' => "Key '$key' existiert bereits in Settings::\$exl",
        ]);
    }

    // Neuen Eintrag hinzufügen
    $current[$key] = $desc;

    // Nun Datei neu schreiben
    $content = file_get_contents($path);

    // EXAKT das exl-Array ersetzen
    $newExl = "public static array \$exl = [\n";

    foreach ($current as $k => $v) {
        $newExl .= "        '$k' => '" . addslashes($v) . "',\n";
    }

    $newExl .= "    ];";

    // Ersetzt nur das exl-Array
    $content = preg_replace(
        '/public static array \$exl = \[(.*?)\];/s',
        $newExl,
        $content
    );

    file_put_contents($path, $content);

    return response()->json([
        'message' => "Feld $column wurde gespeichert und Settings::\$exl wurde erweitert",
        'added_to_exl' => [$key => $desc]
    ]);
}


public function allTableRights($right)
{
    // Beispiel: $right = "view_table"
    $bin = auth()->user()->rights->$right; // z.B. "1011"

    // Hole alle Tabellen aus admin_tables
    $tables = DB::table('admin_table')->orderBy("name","ASC")->pluck('name')->toArray();

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
