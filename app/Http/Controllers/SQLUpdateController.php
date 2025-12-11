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

    public function __construct()
    {
        $this->domset  = "mariadb_ol";
        $this->opsFile = "lastmySQLOps.dat";
    }

    public function index()
    {
        return Inertia::render('Admin/SQLUpdate');
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
    public function tables()
    {
        return response()->json([
            'local'  => $this->getTablesWithHash(DB::getDefaultConnection()),
            'online' => $this->getTablesWithHash($this->domset),
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
    //         $hash = $this->getTableHash($t['name'], DB::getDefaultConnection());

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

    private function getTablesWithHash(string $connection): array
    {
        $database = DB::connection($connection)->getDatabaseName();

        $tables = DB::connection($connection)->select("
            SELECT TABLE_NAME AS name
            FROM information_schema.tables
            WHERE TABLE_SCHEMA = ?
        ", [$database]);

        // Tabellen filtern
        $tables = array_filter($tables, fn($t) => !in_array($t->name, Settings::$excl_dump_tables));
        usort($tables, fn($a, $b) => strcmp($a->name, $b->name));

        $result = [];
        $dom = SD();

        foreach ($tables as $t) {
            $table = $t->name;

            // aktuellen Hash berechnen
            $hashLocal  = $this->getTableHash($table, DB::getDefaultConnection());
            $hashOnline = $this->getTableHash($table, $this->domset);

            // gespeicherte Hashes abrufen
            $storedLocal = DB::table('dbhash')
                ->where('dom', $dom)->where('table', $table)->where('online', 'lh')
                ->value('hash');

            $storedOnline = DB::table('dbhash')
                ->where('dom', $dom)->where('table', $table)->where('online', 'rh')
                ->value('hash');

            // Wenn Hash NULL → aktuellen Hash speichern, nur einmal
            if ($storedLocal === null) {
                $this->AddHash($table, $hashLocal, 'lh');
                $storedLocal = $hashLocal;
            }

            if ($storedOnline === null) {
                $this->AddHash($table, $hashOnline, 'rh');
                $storedOnline = $hashOnline;
            }

            // Farben bestimmen
            if ($hashLocal === $hashOnline) {
                // Beide Seiten identisch → rot
                $offlineColor = 'red';
                $onlineColor  = 'red';
            } else {
                // Local grün, wenn hashLocal != storedLocal
                $offlineColor = ($hashLocal !== $storedLocal && $hashLocal !== $hashOnline && $hashLocal !== $storedOnline) ? 'green' : 'red';
                $onlineColor  = ($hashOnline !== $storedOnline && $hashLocal !== $hashOnline && $hashOnline !== $storedLocal) ? 'green' : 'red';
            }

            // Debug-Log
if($table == "image_categories"){
                \Log::info("Table: $table | hashLocal: $hashLocal | hashOnline: $hashOnline | storedLocal: $storedLocal | storedOnline: $storedOnline | offlineColor: $offlineColor | onlineColor: $onlineColor");
            }

            // Hash-Datensätze neu anlegen falls nicht vorhanden
            $this->AddHash($table, null, 'lh');
            $this->AddHash($table, null, 'rh');

            $result[] = [
                'name'        => $table,
                'hash'        => $hashLocal,
                'hash_online' => $hashOnline,
                'col_offline' => $offlineColor,
                'col_online'  => $onlineColor,
            ];
        }

        return $result;
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
                $con === 'lh' ? DB::getDefaultConnection() : $this->domset
            );

            // Hash speichern
            $this->UpdateHash($table, $hash, $con);
        }

        Storage::put($this->opsFile, now()->format('Y-m-d H:i:s'));

        return response()->json(['success' => true]);
    }

    private function syncLocalToOnline(string $table)
    {
        $data = DB::table($table)->get();
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table($table)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($data as $row) {
            DB::table($table)->insert((array)$row);
        }
    }

    private function UpdateHash($table, $hash, $con='lh')
    {
        $dom = SD();

        DB::table("dbhash")
            ->where("dom", $dom)
            ->where("table", $table)
            ->where("online", $con)
            ->update([
                'hash'       => $hash,
                'updated_at' => now(),
            ]);
    }

    private function AddHash($table, $hash, $con='lh')
    {
        $dom = SD();
        $online = $con === "lh" ? "lh" : "rh";

        // Wenn bereits existiert → niemals überschreiben
        $exists = DB::table("dbhash")
            ->where("dom", $dom)
            ->where("table", $table)
            ->where("online", $online)
            ->exists();

        if ($exists) return;

        // Neuer Eintrag, aber hash IMMER null
        DB::table("dbhash")->insert([
            'dom'        => $dom,
            'table'      => $table,
            'online'     => $online,
            'hash'       => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
