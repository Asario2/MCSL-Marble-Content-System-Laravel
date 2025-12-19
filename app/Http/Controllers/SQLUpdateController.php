<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;

class SQLUpdateController extends Controller
{
    private string $opsFile;
    private string $domset;
    private string $domset_of;
    private array $KTables;

    public function __construct()
    {
        $_SESSION['domain'] ?? "ab";
        $this->domset  = $this->GetDBCon(1,@$_SESSION['domain']);
        $this->domset_of = $this->GetDBCon(0,@$_SESSION['domain']);
        $this->adb = $this->GetDBBase();
        $this->KTables = ["cleo_","ffrog_","monxx_"];
        $this->opsFile = "lastmySQLOps.dat";
        if(!session_id())
        {
            session_start();
        }
    }
    public function Ignore_Field(Request $request)
    {
        $dom = $request->domain;
        $table = $request->table;
        $col = $request->col;

        $settingsPath = app_path("Models/Settings.php");

        // Aktuelle Settings laden
        $cont = file_get_contents($settingsPath);

        // Variable aus der Datei evalen (nur $Ignored_Field)
        $Ignored_Field = [];
        if (preg_match('/public static array \$Ignored_Field\s*=\s*\[(.*?)\];/s', $cont, $matches)) {
            // Inhalt in Array umwandeln
            $code = '$tmp = [' . $matches[1] . '];';
            eval($code); // setzt $tmp
            $Ignored_Field = $tmp ?? [];
        }

        // Neue Kombination
        $newEntry = $dom . "_" . $table . "_" . $col;

        // Hinzufügen, wenn noch nicht vorhanden
        if (!in_array($newEntry, $Ignored_Field)) {
            $Ignored_Field[] = $newEntry;
        }

        // Array als String formatieren
        $arrayString = "public static array \$Ignored_Field = [\n";
        foreach ($Ignored_Field as $key => $val) {
            $arrayString .= "    $key => '$val',\n";
        }
        $arrayString .= "];";

        // Alten $Ignored_Field Block ersetzen oder hinzufügen
        if (preg_match('/public static array \$Ignored_Field\s*=\s*\[.*?\];/s', $cont)) {
            $newCont = preg_replace('/public static array \$Ignored_Field\s*=\s*\[.*?\];/s', $arrayString, $cont);
        } else {
            // Falls noch nicht vorhanden: ans Ende anhängen
            $newCont = $cont . "\n" . $arrayString;
        }

        // Speichern
        file_put_contents($settingsPath, $newCont);
    }

    public function GetDBBase()
    {
//        return Settings::$con_db[SD()];
    }
    public function GetDBCon($reg=0,$domain="ab")
    {
        $domain = $domain ?? "ab";
        // \Log::info("SET:".json_encode(Settings::$connect_dbname));

        if($reg == 1){
            // dd(Settings::$connect_dbname[SD()]."_ol");
            return Settings::$connect_dbname[$domain]."_ol";
        }
        return Settings::$connect_dbname[$domain];

    }
    public function index()
    {
        if(!CheckZRights("SQLUpdate"))
        {
            header("Location: /no-rights");
            exit;
        }
        $cor_date = date("d.m.Y H:i:s",strtotime(file_get_contents(storage_path("/app/".$this->opsFile))));
        return Inertia::render('Admin/SQLUpdate',compact("cor_date"));
    }

    public function last()
    {
        if (!Storage::exists($this->opsFile)) {
            return response()->json([
                'lastDate' => '1970-01-01 00:00:00',
                'cor_date' => '01.01.1970 00:00:00'
            ]);
        }

        $date = trim(Storage::get($this->opsFile));

        return response()->json([
            'lastDate' => $date,
            'cor_date' => date("d.m.Y H:i:s", strtotime($date))
        ]);
    }

    // ---------------------------
    // Tabellen + Hashes
    // ---------------------------
    public function tables(Request $request,$domain='ab')
    {
        $this->domset  = $this->GetDBCon(1,$request->domain);
        $this->domset_of = $this->GetDBCon(0,$request->domain);
        $_SESSION['domain'] = $domain;

        \Log::info("DOM: ".$domain);

        return response()->json([
            'local'  => $this->getTablesWithHash($this->domset_of,$request->domain),
            'online' => $this->getTablesWithHash($this->domset,$request->domain),
        ]);
    }
    // public function syncToAll()
    // {
    //     $dom = SD();

    //     // Tabellen + Status laden
    //     $tables = $this->tables()->getData(true);

    //     $local  = collect($tables['local']);
    //     $online = collect($tables['online']);

    //     // beide Arrays anhand der Tabellennamen zusammenführen
    //     $merged = $local->map(function ($item) use ($online) {
    //         $on = $online->firstWhere('name', $item['name']);

    //         return [
    //             'name'        => $item['name'],
    //             'hash_local'  => $item['hash'],
    //             'hash_online' => $on['hash_online'] ?? null,
    //             'col_local'   => $item['col_offline'],
    //             'col_online'  => $on['col_online'] ?? null,
    //         ];
    //     });

    //     // -----------------------
    //     // 1. ONLINE → LOCAL Sync
    //     // -----------------------
    //     $onlineToLocal = $merged->filter(fn ($t) =>
    //         $t['col_online'] === 'green' &&
    //         $t['col_local']  === 'red'
    //     );

    //     foreach ($onlineToLocal as $t) {
    //         $this->syncOnlineToLocal($t['name']);

    //         // neuen Hash berechnen
    //         $hash = $this->getTableHash($t['name'], $this->domset_of);

    //         // Hash speichern (offline = lh)
    //         $this->UpdateHash($t['name'], $hash, 'lh');
    //     }

    //     // -----------------------
    //     // 2. LOCAL → ONLINE Sync
    //     // -----------------------
    //     $localToOnline = $merged->filter(fn ($t) =>
    //         $t['col_local'] === 'green' &&
    //         $t['col_online']  === 'red'
    //     );

    //     foreach ($localToOnline as $t) {
    //         $this->syncLocalToOnline($t['name']);

    //         // neuen Hash berechnen
    //         $hash = $this->getTableHash($t['name'], $this->domset);

    //         // Hash speichern (online = rh)
    //         $this->UpdateHash($t['name'], $hash, 'rh');
    //     }

    //     // Timestamp aktualisieren
    //     Storage::put($this->opsFile, now()->format('Y-m-d H:i:s'));

    //     return response()->json([
    //         'success' => true,
    //         'online_to_local' => $onlineToLocal->pluck('name'),
    //         'local_to_online' => $localToOnline->pluck('name'),
    //     ]);
    // }
    private function getColumnHashes(string $table, string $connection): array
    {
        $rows = DB::connection($connection)->table($table)->get();

        $hashes = [];

        foreach ($rows as $row) {
            foreach ((array) $row as $col => $value) {
                $hashes[$col] = md5(($hashes[$col] ?? '') . '|' . serialize($value));
            }
        }

        return $hashes;
    }
    private function getTablesWithHash(string $connection, string $domain): array
    {
        $database = DB::connection($connection)->getDatabaseName();

        // Tabellen laden
        $tables = DB::connection($connection)->select("
            SELECT TABLE_NAME AS name
            FROM information_schema.tables
            WHERE TABLE_SCHEMA = ?
        ", [$database]);

        // Tabellen filtern
        $tables = array_filter($tables, fn ($t) =>
            !in_array($t->name, Settings::$excl_dump_tables, true)
        );

        $tables = array_filter($tables, function ($t) {
            foreach ($this->KTables as $prefix) {
                if (str_starts_with($t->name, $prefix)) {
                    return false;
                }
            }
            return true;
        });

        usort($tables, fn ($a, $b) => strcmp($a->name, $b->name));

        $result = [];

        foreach ($tables as $t) {

            $table = $t->name;

            /* --------------------------------------------------
            * Hashes berechnen
            * -------------------------------------------------- */
            $hashLocal  = $this->getTableHash($table, $this->domset_of);
            $hashOnline = $this->getTableHash($table, $this->domset);

            /* --------------------------------------------------
            * Gespeicherte Hashes
            * -------------------------------------------------- */
            $storedLocal = DB::connection('mariadb')->table('dbhash')
                ->where('dom', $domain)
                ->where('table', $table)
                ->where('online', 'lh')
                ->value('hash');

            $storedOnline = DB::connection('mariadb')->table('dbhash')
                ->where('dom', $domain)
                ->where('table', $table)
                ->where('online', 'rh')
                ->value('hash');

            if ($storedLocal === null) {
                $this->AddHash($table, $hashLocal, 'lh', 1);
                $storedLocal = $hashLocal;
            }

            if ($storedOnline === null) {
                $this->AddHash($table, $hashOnline, 'rh', 1);
                $storedOnline = $hashOnline;
            }

            /* --------------------------------------------------
            * Wenn komplett identisch → überspringen
            * -------------------------------------------------- */
            if ($hashLocal === $hashOnline) {
                continue;
            }

            /* --------------------------------------------------
            * Spalten-Hashes laden
            * -------------------------------------------------- */
            $localCols  = $this->getColumnHashes($table, $this->domset_of);
            $onlineCols = $this->getColumnHashes($table, $this->domset);

            $changedCols = [];

            foreach ($localCols as $col => $hash) {
                if (!isset($onlineCols[$col])) {
                    continue;
                }

                if ($hash !== $onlineCols[$col]) {
                    $changedCols[] = $col;
                }
            }

            /* --------------------------------------------------
            * Ignorierte Spalten filtern
            * -------------------------------------------------- */
            $effectiveChangedCols = [];

            foreach ($changedCols as $col) {
                $key = $domain . '_' . $table . '_' . $col;

                if (!in_array($key, Settings::$Ignored_Field, true)) {
                    $effectiveChangedCols[] = $col;
                }
            }

            // ❌ Alle Änderungen ignoriert → Tabelle NICHT anzeigen
            if (count($effectiveChangedCols) === 0) {
                continue;
            }

            /* --------------------------------------------------
            * Farben bestimmen
            * -------------------------------------------------- */
            $offlineColor = (
                $hashLocal !== $storedLocal &&
                $hashLocal !== $hashOnline &&
                $hashLocal !== $storedOnline
            ) ? 'green' : 'red';

            $onlineColor = (
                $hashOnline !== $storedOnline &&
                $hashLocal !== $hashOnline &&
                $hashOnline !== $storedLocal
            ) ? 'green' : 'red';

            if ($offlineColor === 'red' && $onlineColor === 'red') {
                continue;
            }

            /* --------------------------------------------------
            * Ergebnis
            * -------------------------------------------------- */
            $result[] = [
                'name'          => $table,
                'hash'          => $hashLocal,
                'hash_online'   => $hashOnline,
                'col_offline'   => $offlineColor,
                'col_online'    => $onlineColor,
                'changed_cols'  => $effectiveChangedCols, // NUR relevante
            ];
        }

        return $result;
    }


public function diffTable(string $table, string $domain)
{
    $this->domset     = $this->GetDBCon(1, $domain);
    $this->domset_of  = $this->GetDBCon(0, $domain);
    $_SESSION['domain'] = $domain;

    $localRows  = DB::connection($this->domset_of)->table($table)->get()->toArray();
    $onlineRows = DB::connection($this->domset)->table($table)->get()->toArray();

    $local  = json_decode(json_encode($localRows), true);
    $online = json_decode(json_encode($onlineRows), true);

    // ✅ Headline-Spalte aus Settings
    $headlineColumn = Settings::$headline[$table] ?? null;

    $diff = [];
    $max = max(count($local), count($online));

    for ($i = 0; $i < $max; $i++) {
        $localRow  = $local[$i]  ?? [];
        $onlineRow = $online[$i] ?? [];

        $rowDiff = [];
        $columns = array_unique(array_merge(
            array_keys($localRow),
            array_keys($onlineRow)
        ));

        foreach ($columns as $col) {
            $localVal  = $localRow[$col]  ?? null;
            $onlineVal = $onlineRow[$col] ?? null;

            if ($localVal !== $onlineVal && !in_array($domain."_".$table."_".$col,Settings::$Ignored_Field)) {
                $rowDiff[$col] = [
                    'local'  => $localVal,
                    'online' => $onlineVal,
                ];
            }
        }

        if (!empty($rowDiff)) {
            $diff[] = [
                'row' => $i,
                "id"=>$localRow['id'] ?? $onlineRow['id'],

                // ⭐ genau das, was du willst
                'name' =>  $localRow[$headlineColumn] ?? null,


                'changes' => $rowDiff,
            ];
        }
    }

    return response()->json([
        'table' => $table,
        'diff'  => $diff,
    ]);
}





    private function getTableHash(string $table, string $connection)
    {
        if (in_array($table, Settings::$excl_dump_tables)) return null;

        $columns = DB::connection($connection)->getSchemaBuilder()->getColumnListing($table);
        if (empty($columns)) return md5("EMPTY");

        sort($columns);

        $query = DB::connection($connection)->table($table)->select($columns);
        foreach ($columns as $col) $query->orderBy($col, 'asc');

        $rows = $query->get();
        if ($rows->isEmpty()) return md5("EMPTY");

        $cleanRows = $rows->map(function($row) use ($columns){
            $arr = [];
            foreach ($columns as $c) $arr[$c] = $row->$c;
            return $arr;
        })->values();

        return md5(json_encode($cleanRows, JSON_UNESCAPED_UNICODE));
    }

    public function sync(Request $request)
    {
        $direction = $request->input('direction', 'local_to_online');
        $tables    = $request->input('tables', []);

        foreach ($tables as $table) {
            if ($direction === 'local_to_online') {
                $this->syncLocalToOnline($table);
                $con = 'lh';
            } else {
                $this->syncOnlineToLocal($table);
                $con = 'rh';
            }

            // neuen Hash nach dem Sync berechnen
            $hash = $this->getTableHash(
                $table,
                $con === 'lh' ? $this->domset_of : $this->domset
            );

            // Hash speichern
            $this->UpdateHash($table, $hash, $con);
        }

        Storage::put($this->opsFile, now()->format('Y-m-d H:i:s'));

        return response()->json(['success' => true]);
    }

    private function syncLocalToOnline(string $table)
    {
        $data = DB::connection($this->domset_of)->table($table)->get();
        DB::connection($this->domset)->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection($this->domset)->table($table)->delete();
        DB::connection($this->domset)->statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($data as $row) {
            DB::connection($this->domset)->table($table)->insert((array)$row);
        }
    }

    private function syncOnlineToLocal(string $table)
    {
        $data = DB::connection($this->domset)->table($table)->get();
        DB::connection($this->domset_of)->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection($this->domset_of)->table($table)->delete();
        DB::connection($this->domset_of)->statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($data as $row) {
            DB::connection($this->domset_of)->table($table)->insert((array)$row);
        }
    }

    private function UpdateHash($table, $hash, $con='lh')
    {
        $dom = $_SESSION['domain'];

        DB::connection("mariadb")->table("dbhash")
            ->where("dom", $dom)
            ->where("table", $table)
            ->where("online", $con)
            ->update([
                'hash'       => $hash,
                'updated_at' => now(),
            ]);
    }

    private function AddHash($table, $hash, $con='lh',$ov=false)
    {
        $dom = $_SESSION['domain'];
        $online = $con === "lh" ? "lh" : "rh";
        if($ov)
        {
            // DB::connection("mariadb")->table("dbhash")
            // ->where("dom", $dom)
            // ->where("table", $table)
            // ->where("online", $online)
            // ->update(["hash"=>md5('test')]);
        }
        // Wenn bereits existiert → niemals überschreiben
        $exists = DB::connection("mariadb")->table("dbhash")
            ->where("dom", $dom)
            ->where("table", $table)
            ->where("online", $online)
            ->exists();

        if ($exists) return;

        // Neuer Eintrag, aber hash IMMER null
        DB::connection("mariadb")->table("dbhash")->insert([
            'dom'        => $dom,
            'table'      => $table,
            'online'     => $online,
            'hash'       => null,
            'updated_at' => now(),
        ]);
    }

}
