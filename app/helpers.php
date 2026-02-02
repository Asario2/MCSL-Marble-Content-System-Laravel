<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\EncryptController;
use App\Http\Controllers\OldEncController;
use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
    if(!function_exists("ucf"))
    {
        function ucf($str)
        {
            $arr = explode("_",$str);

            if(!is_array($arr))
            {
                return ucfirst($str);
            }
            foreach($arr as $val)
            {
                $na[] = ucfirst($val);
            }
            return implode(" ",$na);

        }
    }
    if(!function_exists("MCSL_GRAD"))
    {
        function MCSL_GRAD(){
            return "<style>BODY{font-family:Tahome !important;}</style>
            <div style=\"background-color:#000;background-position:right;height:52px;background-repeat:no-repeat;background-image:url('https://www.marblefx.de/_images/mailheader/mcsl_grad.png');\">
            <div style=\"float:left;position:relative;margin-top:0px;margin-left:0px;\">
            <img src=\"https://www.marblefx.de/_images/mailheader/mcsl_mail_system2.png\" alt=\"MCS Mail System\" title=\"MCS Mail System\">
            </div></div>
            <style>
            .button-primary{
                    background-color:#222;
                    padding:5px 7px !important;
                    border-radius:6px;
                    border:2px solid #ffc600;
                    line-height:20px;
                    font-size:20px;
                    color:#ffc600;
                    font-family:Tahoma;
                    margin:3px;
                    text-decoration:none;
                }.button-primary:hover,a.button-primary:visited:hover{
                background-color:#ffc600 !important;
                color:#222 !important;
                border:3px solid #ffc600 !important;
                padding:5px 7px !important;
                }
                .ora{
                    color:#ffc600;
                    font-weight:bold;
            }
            .w-half{
                width:600px;
                max-width:100%;
            }
            .ncol{
                color:#3b82f6;
            }
            </style>
        <br />";
        }
    }
    if(!function_exists("decval_old"))
    {
        function decval_old($data)
        {

{
    if(!$data)
    {
        return;
    }
    $data = strip_tags($data);
    $data = trim($data);
    $enc = NEW OldEncController();
    if($enc->decryptString($enc->decryptString($enc->decryptString($enc->decryptString($data)))) !== false)
    {
    return $enc->decryptString($enc->decryptString($enc->decryptString($enc->decryptString($data))));
    }

    if($enc->decryptString($enc->decryptString($enc->decryptString($data))   ) !== false)
    {
    return $enc->decryptString($enc->decryptString($enc->decryptString($data)));
    }

    if($enc->decryptString($enc->decryptString($data)) !== false)
    {
        return $enc->decryptString($enc->decryptString($data));
    }
    if($enc->decryptString($data) !== false)
    {
        return $enc->decryptString($data);
    }
    if($enc->decryptString($data) !== false)
    {
        return $enc->decryptString($data);
    }
    return $data;

        }
    }
}
    if(!function_exists("DUMP_DB"))
    {
        function DUMP_DB()
        {
            return "<a href='dump://parameter1/parameter2'>Start Dump</a>";
        }
    }
if (!function_exists('Notify')) {

    function Notify()
    {
        $entries = DB::table('genxlo.notifications')->get();

        $output = '';
        $xx = '';
        foreach ($entries as $entry) {

            $entry = (array) $entry;

            // --------------------------------------------------
            // 1️⃣ Zeitpunkt aus timespy-Datei lesen
            // --------------------------------------------------
            $time = null;
            $timespyPath = public_path('/timespy/' . ($entry['timespy'] ?? ''));

            if (!empty($entry['timespy']) && file_exists($timespyPath)) {

                // $tfile = rtrim(file_get_contents($timespyPath));

                $tfile = preg_replace('/^\x{FEFF}/u', '', file_get_contents($timespyPath));
                $tfile = trim($tfile);

                if ($tfile !== '' && Carbon::hasFormat($tfile, 'Y-m-d H:i:s')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $tfile);
                }

                // $time = trim($tfile);
            }
            // dd($time);

            // --------------------------------------------------
            // 2️⃣ Host-Logik (localhost / online / both)
            // --------------------------------------------------
            $lh = false;
            $host = request()->getHost();

            if ($entry['type'] === 'localhost' && str_contains($host, 'test.mcs')) {
                $lh = true;
            } elseif ($entry['type'] === 'online' && !str_contains($host, 'test.mcs')) {
                $lh = true;
            } elseif ($entry['type'] === 'both') {
                $lh = true;
            }

            // --------------------------------------------------
            // 3️⃣ Ablaufdatum berechnen
            // --------------------------------------------------
            $days = (int) ($entry['days'] ?? 0);
            $expiresAt = null;

            if ($time instanceof Carbon) {
                $expiresAt = $time->copy()->addDays($days);
            }

            // --------------------------------------------------
            // 4️⃣ Anzeige prüfen
            // --------------------------------------------------
            if (
                $lh &&
                (
                    !$time ||
                    ($expiresAt && now()->greaterThan($expiresAt))
                )
            ) {
//                 dd(
//     file_get_contents($timespyPath),
//     bin2hex(file_get_contents($timespyPath))
// );

//                 dd($time,$expiresAt);
                // $output .= "<div style='background:#ccc;color:#002;padding:10px;margin-bottom:10px;z-index:999999'>";

                $output .= "<h3>{$entry['name']}</h3>";
                $output .= $entry['text']." ";

                if (!empty($entry['function_link']) && function_exists($entry['function_link'])) {
                    $fn = $entry['function_link'];
                    $output .= $fn();
                }
                $status = $entry['status'];
                $seconds = $entry['seconds'];
                $xx .= 'window.toastBus.emit({ message: "'.$output.'", type: "'.$status.'", duration: '.$seconds.' });';


                // ⏱️ Zeitpunkt speichern

            }
        }

        if ($xx) {
            return $xx;

        }
    }
}



    if (!function_exists('KILLMD')) {
    function KILLMD($text,$count='45') {
        // Entferne Markdown (z. B. durch reguläre Ausdrücke)
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text); // Entfernt **bold**
        $text = preg_replace('/\*(.*?)\*/', '$1', $text); // Entfernt *italic*
        $text = preg_replace('/\#\s?(.*)/', '$1', $text); // Entfernt # Überschriften
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $text); // Entfernt Links
        $text = strip_tags($text); // Entfernt HTML-Tags
        return shorter($text,$count);
    }
}
if(!function_exists("SD_ALT")){
    function SD_ALT() {
        return app()->runningInConsole()
            ? 'ab'   // Fallback, wenn kein Request existiert
            : explode('.', str_replace("www.",'',request()->getHost()))[0];
    }
}
if(!function_exists("encval"))
{
function encval($value)
{
    if ($value === null || $value === '') {
        return $value;
    }
    return Crypt::encryptString((string) $value);
}
}
if(!function_exists("decval"))
{
function decval($value)
{
    if ($value === null || $value === '') {
        return $value;
    }
    try {
        return Crypt::decryptString($value);
    } catch (\Exception $e) {
        return $value; // Fallback falls schon im Klartext
    }
}
}
if(!function_exists("encval_new"))
{
function encval_new($value)
{
    if ($value === null || $value === '') {
        return $value;
    }

    return hash('sha256', trim(decval($value)));
}
}
if(!function_exists("decval_new"))
{
function decval_new($value)
{
    if ($value === null || $value === '') {
        return $value;
    }
    try {
        return hash('sha256', $value);
    } catch (\Exception $e) {
        return $value; // Fallback falls schon im Klartext
    }
}
}
if(!function_exists("RUMLAUT"))
{
    function RUMLAUT($string,$table='')
    {
        $string = preg_replace('/<\/li>\s*<br\s*\/?>/i', '</li>', $string);
        $string = preg_replace('/<li>\s*<br\s*\/?>/i', '<li>', $string);
        $string = preg_replace([
            '/<br\s*\/?>\s*(<(h2|h3|p)[^>]*>)/i',   // <br> VOR <h2>/<h3>/<p>
            '/(<\/(h2|h3|p)>)\s*<br\s*\/?>/i'       // <br> NACH </h2></h3></p>
        ], [
            '$1',
            '$1'
        ], $string);
        if($table == "shortpoems" || $table == "didyouknow")
        {
            $string = str_replace(["<p>","</p>","<br />"],'',$string);
        }
        $string = str_replace("â€“","-",$string);
        $string = html_entity_decode($string);
        return str_replace(array("â€“","---","ÃƒÅ“","ÃƒÂ¼",'ÃƒÅ¸','Ãƒ?','ÃƒÂ¤','â€™',"Ã„",'Ãœ',"Ã","Ã¶",'Ã"Y','Ã¼','Ã¤','ÃŸ',"âEUR¦",'Ã?','ÃƒÂ¶'),array("-","<hr>","&Uuml;",'&uuml;',"&szlig;","&szlig;","&auml;","'","&Auml;","&Uuml;","&szlig;","&ouml;","&szlig;","&uuml;","&auml;","&szlig;",'',"&szlig;","&ouml;"),$string);
    }
}
if(!function_exists("shorter"))
{
    function shorter($str,$count)
    {
        $encoding = mb_detect_encoding($str, ["UTF-8", "ISO-8859-1", "Windows-1252"], true);

    if ($encoding !== "UTF-8") {
        $str = iconv($encoding, "UTF-8//IGNORE", $str);
    }
    $string = $str;
        $string = RUMLAUT($string);
        $str2 = wordwrap($string,$count,"<br />");
        $str3 = explode("<br />",$str2);
        if(@$str3[5])
        {
            $hl = "&hellip;";
        }
        $str4 = $str3[0]." ".@$str3[1]." ".@$str3[2]." ".@$str3[3]." ".@$str3[4]." ".@$hl;
        return $str4;
    }
}
if(!function_exists("renderText"))
{
    function renderText($txt)
    {
        return html_entity_decode(html_entity_decode(smi(htmlentities(nl2br($txt)))));
    }
}
// if (!Builder::hasMacro('filterdefault')) {
//     Builder::macro('filterdefault', function ($filters) {
//         if (!empty($filters['search'])) {
//             $table = request('table', session('table')); // Sichere Abfrage der Tabelle

//             if (!$table) {
//                 return $this; // Falls keine Tabelle definiert, beenden
//             }

//             session(['table' => $table]); // Tabelle in der Session speichern

//             $whvals = Settings::$searchFields[$table] ?? []; // Hole Suchfelder

//             return $this->where(function ($query) use ($table, $filters, $whvals) {
//                 foreach ($whvals as $whn) {
//                     $query->orWhereRaw("LOWER(`$table`.`$whn`) LIKE ?", ['%' . strtolower(htmlentities($filters['search'])) . '%']);
//                 }

//                 $columns = Schema::getColumnListing($table);
//                 if (in_array("created_at", $columns)) {
//                     $query->orWhere("$table.created_at", 'like', '%' . $filters['search'] . '%');
//                 }

//                 $query->orWhere("$table.id", 'like', '%' . $filters['search'] . '%');
//             });
//         }

//         return $this;
//     });
// }

        use Illuminate\Database\Query\Builder;
        use App\Models\Settings;
        use Illuminate\Support\Facades\Session;

      if (!Builder::hasMacro('filterdefault')) {
    Builder::macro('filterdefault', function ($filters) {
        $path = request()->path();
        $path = strtolower($path);
        $parts = explode("/", $path);

        foreach(gettables() as $ta) {
            if(in_array($ta,$parts)) {
                $table = $ta;
                $_GET['table'] = $ta;
            }
        }

        if(@$table == "pictures" || $table == "image_categories") {
            $table = "images";
            $_GET['table'] = $table;
        }

        if (!empty($filters['search'])) {
            $searchTerm = html_entity_decode(strtolower($filters['search']));

//             \Log::info("=== START FILTERDEBUG ===");
//             \Log::info("Searching in table: " . $table);
//             \Log::info("Search term: " . $filters['search']);

            $whvals = @Settings::$searchFields[$table] ?? [];
            $subvals = @Settings::$searchFields[$table];
            $whh = [];

            foreach($subvals as $key=>$val) {
                if(substr_count($val,".")) {
                    $whh[$table][$key] = explode(".",Settings::$searchFields[$table][$key])[0];
                    $whvals[] = $whh[$table][$key];
                }
            }

//             \Log::info("Search fields: " . json_encode($whvals));

            // Zuerst alle anderen Felder durchsuchen
            foreach ($whvals as $whn) {
                if(@is_array($whh[$table])) {
                    foreach($whh[$table] as $skey=>$sval){
                        $this->orWhereRaw("LOWER(`$sval`.`name`) LIKE ?", ['%' . $searchTerm . '%']);
//                         \Log::info("Added search for related table: $sval.name");
                    }
                }

                if(!substr_count($whn,".") && Schema::hasColumn($table, $whn)) {
                    $this->orWhereRaw("LOWER(`$table`.`$whn`) LIKE ?", ['%' . $searchTerm . '%']);
//                     \Log::info("Added search for column: $table.$whn");
                }
            }

            $columns = Schema::getColumnListing($table);
//             \Log::info("Available columns: " . implode(', ', $columns));

            // created_at Suche
            if(in_array("created_at",$columns)){
//                 \Log::info("CREATED_AT column exists - adding search conditions");

                // Einfache LIKE-Suche
                $this->orWhere("$table.created_at", 'like', '%'. $filters['search'] . '%');
//                 \Log::info("Added simple LIKE for created_at: " . $filters['search']);

                // Debug: SQL Query vor created_at
//                 \Log::info("SQL before created_at: " . $this->toSql());
//                 \Log::info("Bindings before created_at: " . json_encode($this->getBindings()));
            }

            if(in_array("date_begin",$columns)){
                $this->orWhere("$table.date_begin", 'like', '%'. $filters['search'] . '%');
//                 \Log::info("Added search for date_begin");
            }

            // ID als letztes hinzufügen
            $this->orWhere("$table.id", 'like', '%'. $filters['search'] . '%');
//             \Log::info("Added search for ID");

            // Debug: Finale SQL Query
//             \Log::info("Final SQL: " . $this->toSql());
//             \Log::info("Final Bindings: " . json_encode($this->getBindings()));
//             \Log::info("=== END FILTERDEBUG ===");
        }

        return $this;
    });
}
if(!function_exists("gettables"))
{
    function gettables()
    {
        $tables = DB::table("admin_table")->pluck("name");
        $tables[] = "pictures";
        // \Log::info("asd:".json_encode($tables));
        return $tables;
    }
}
if(!function_exists("renderMarkdown"))
{
    function renderMarkdown($markdown)
    {
        // $markdown_alt = str_replace(array("<br />#") , "<br />\n#",$markdown);
        // $a = strlen($markdown_alt);
        $markdown  = killbr_alt($markdown);
        // var_dump($a,$b);
        // exit;
        $markdown2 = $markdown;
        $markdown3 = preg_replace_callback('/^(#{1,6})\s+(.*)$/mU', function ($matches) {
            $level = strlen($matches[1]); // Anzahl der # bestimmt die Ebene
            return "<h{$level}>{$matches[2]}</h{$level}>";
        }, $markdown2);
        $markdown  = str_replace('´','`',$markdown);
        $markdown3 = $markdown;

        //$environment->addExtension(new CommonMarkCoreExtension());
       // dd($markdown3);
        // $converter = new CommonMarkConverter([], $environment);
        // dd(Str::markdown('**Test**'));
        $converter = new CommonMarkConverter([
             'html_input' => 'escape', // Option: HTML im Markdown wird behandelt
                 'allow_unsafe_links' => true,
        ]);

        return Str::markdown($markdown3);
    }
}
if(!function_exists("smi"))
{
    function smi($string)
    {
        $string = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
        $string = iconv('ISO-8859-1','UTF-8', $string);


        // $string = stripslashes($string);

        $string = str_replace("\n", " ", $string);
        $string = str_replace(" #","#",$string);
        $string = html_entity_decode($string);
        $string = str_replace("<br />#","<br />\n#",$string);
        $string = str_replace("<p></p>","\n",$string);
        $string = renderMarkdown($string);
        $string = RUMLAUT($string);
        return $string;
    }
}
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\MarkDownController;
if(!session_id())
{
    @session_start();
}
if (!function_exists('vde')) {
    function vde($val1,$val2='',$val3='',$val4='')
{
    echo "<pre>";
        if(isset($val4))
        {
            var_dump($val1,@$val2,@$val3,@$val4);
        }
        elseif(isset($val3))
        {
            var_dump($val1,@$val2,@$val3);
        }
        elseif(isset($val2))
        {
            var_dump($val1,@$val2);
        }
        elseif(isset($val1))
        {
            var_dump($val1);
        }
        else
        {
            var_dump($val1);
        }

        echo "</pre>";
        exit;
}
if(!function_exists('vd'))
{
    function vd($val1,$val2='',$val3='',$val4='')
{
    echo "<pre>";
        if(!empty($val4) || is_null($val4))
        {
            var_dump($val1,@$val2,@$val3,@$val4);
        }
        elseif(!empty($val3) || is_null($val3))
        {
            var_dump($val1,@$val2,@$val3);
        }
        elseif(!empty($val2) || is_null($val2))
        {
            var_dump($val1,@$val2);
        }
        elseif(!empty($val1) || is_null($val1))
        {
            var_dump($val1);
        }
        else
        {
            var_dump($val1);
        }

        echo "</pre>";

}

}

if(!function_exists("smilies"))
 {
function smilies($cont)
{
    return htmlspecialchars_decode(htmlspecialchars($cont));

}
}
if (!function_exists('mcs_mail_version')) {
    function mcs_mail_version()
    {
        define("_frutel_new",null);
        include getcwd()."/../../../mcs_general/inc/version.php";
        return $cfg["massmail_version"];

    }
}
}
if(!function_exists('remDoubleUp'))
{
function remDoubleUp()
        {
            $rights = DB::table("users_rights")->select('id',"view_table","add_table","edit_table",'publish_table',"date_table","delete_table")->get();
            foreach($rights as $right)
            {
                $newRights = [
                'view_table' => substr($right->view_table,0,-1),
                'add_table' => substr($right->add_table,0,-1),
                'edit_table' => substr($right->edit_table,0,-1),
                'publish_table' => substr($right->publish_table,0,-1),
                'delete_table' => substr($right->delete_table,0,-1),
                'date_table' => substr($right->date_table,0,-1),
                ];

            DB::table('users_rights')->where("id",$right->id)->update($newRights);
            }
        }
}
if(!function_exists("updatePositions"))
{


    function updatePositions($table) {
    // Anzahl der Einträge in der Tabelle
    $count = DB::table($table)->count(); // Hier wird angenommen, dass es 25 Einträge gibt

    // Aktualisiere die Position für `id = 1` und `id = 2`
    DB::table($table)
        ->where('id', 1)
        ->update(['position' => $count]);

    DB::table($table)
        ->where('id', 2)
        ->update(['position' => $count - 1]);

    // Hole alle anderen Einträge außer `id = 1` und `id = 2`
    $entries = DB::table($table)
        ->whereNotIn('id', [1, 2])
        ->orderBy('id')
        ->get();

    // Beginne bei Position 23 und reduziere die Position für jeden weiteren Eintrag
    $position = $count - 2;

    foreach ($entries as $entry) {
        DB::table($table)
            ->where('id', $entry->id)
            ->update(['position' => $position]);

        $position--;
    }

    return response()->json(['status' => 'success']);
}
}
if(!function_exists("vdd"))
{
    function vdd($val)
    {
        file_put_contents(storage_path("/logs/asd4.log"),@$val);
    }
}
if(!function_exists("pri_ref"))
{
    function pri_ref($message)
    {
        $m = html_entity_decode($message);
        return rinfo_code($m);
    }
}
if(!function_exists("smi"))
{
    function smi($string)
    {
        $string = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
        $string = iconv('ISO-8859-1','UTF-8', $string);

        $string = str_replace(array("---","Ã","Ã¶",'Ã"Y','Ã¼','Ã¤',"âEUR¦"),array("<hr />","&szlig;","&ouml;","&szlig;","&uuml;","&auml;",''),$string);
        $string = stripslashes($string);
        $md = NEW MarkDownController();
        $string = $md->md_to_htm($string);
        return $string;
    }
}
if(!function_exists("TextArea_md"))
{
    function TextArea_md($name,$reqi,$value)
    {
        include resource_path("/cfgz/textarea/lang.php");
        include resource_path("/cfgz/textarea/textarea_md.blade.php");
        return $out2;
    }
}
if(!function_exists("captcha_src"))
{
    function captcha_src()
    {
        return ("/images/captcha.php");
    }
}
if(!function_exists("DevMod"))
{
    function DevMod()
    {
        $_SESSION['Devm'] = @$_SESSION['Devm'] ? '' : true;
        echo "<script>location.href=history.back();</script>";
    }
}
if(!function_exists("shorter"))
{
    function shorter($value)
    {
          $v = wordwrap($value, 25, "<br>", true);
        $v2 = @explode("<br>",$v);
        if(@$v2[5])
        {
            $dotz = '&hellip;';
        }
        $v3 = $v2[0]." ".@$v2[1]." ".@$v2[2]." ".@$v2[3].@$dotz;
        return $v3;
    }
}
if(!function_exists("FormName"))
{
    function FormName($id = '',$tt = '')
    {
        $name = "ggxform";
        (@$_GET['id'] ? $tok = $_GET['id'] : $tok = "");

        if(isset($_GET['t']) && !$id)
        {
            $tok .= "_".@$_GET['t'];
        }
        if(empty($id))
        {
            $tok = "_".$id;
        }
        if($tt)
        {
            $tok .= "_".$tt."_";
        }
        $tok .= "toktokvssofieO";
        $name .= "_".($tok);
        return $name;
    }

}
if(!function_exists('killbr'))
{
    function killbr($text)
    {
        return str_replace("<br />\n<br />","<br />",$text);
    }
}
if(!function_exists("killbr_alt"))
{
    function killbr_alt($str)
    {
        return str_replace("<br />","\n",$str);
    }
}
if(!function_exists("killbr2"))
{
    function killbr2($txt)
    {
        $txt = str_replace(array("&gt;","&lt;","&amp;"),array("&amp;gt;","&amp;lt;","&amp;amp;"),$txt);
        return $txt;
    }
}
if(!function_exists("smiliesboxx"))
{
    function smiliesboxx($name)
    {
        $xx = "";

        @include resource_path("cfgz/cfg.smilies.php");
        $x = 1;
        foreach($smilies as $key => $value)
        {

            $xx .= "<a href='#tf' OnClick=\"AddText_$name('$key');document.getElementById('sm_$name').style.display='none';return false;\" tabindex='-1'>\n<img border='0' alt='$key' title='$key' src='/images/smilies/icon_$value.gif' class='smilie'></a>\n ";
            if(!($x%26))
            {
                $xx .= "<br />";
            }
            $x++;
        }
        return $xx;

    }
}
if(!function_exists("StyleID"))
{
    if(!session_id())
    {
        session_start();
    }
    function StyleID($zza='')
{
    if(!empty($zza))
    {
        $_SESSION['zz']  = 'off';


    }
    if(@$_SESSION['zz'] > 5 && !$zza)
    {
        $_SESSION['zz'] = 0;
    }
    if(@$_SESSION['zz'] === 'off')
    {
        return ;
    }
    @$_SESSION['zz']++;
    return $_SESSION['zz'] ;
}

}
if(!function_exists("ShowRepo"))
{
    function ShowRepo()
    {
        $username = 'OliverReinking'; // GitHub-Benutzername hier angeben
$url = "https://api.github.com/users/$username/repos";

$options = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP' // GitHub API erfordert einen User-Agent-Header
        ]
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$repos = json_decode($response, true);

foreach ($repos as $repo) {
    echo $repo['name']. "<br />" . PHP_EOL;
}


    }
}

if(!function_exists('quotekill'))
{
    function quotekill($text)
    {


    $text = str_replace(array("&quot;&quot;","'quot;","&quot;'"),array("\"","\"","\""),($text));
    $text = str_replace(array("\"'","'\""),"'",$text);
    $text = str_replace("&quot;",'"',$text);
    $text = str_replace("?&copy;","&copy;",$text);
    $text = str_replace(array('',''),array("&ldquo;","&rdquo;"),$text);
    $text = UTF(($text));

    return $text;
    //return stripslashes($text);
    }
}
if(!function_exists("Import_Dump"))
{
    function Import_Dump()
    {
        $sql = file_get_contents(storage_path("dumps/texts.sql"));
        $sea = explode(";",$sql);
        foreach($sea as $query)
        {
            $query = trim($query);
            if(!empty($query))
            {

                DB::statement($query);
            }

        }
        echo "<script>location.href=history.back();</script>";
    }
}
if(!function_exists("readfolders")){

    function readfolders($path = null)
{
    // Standardpfad setzen, wenn keiner übergeben wurde
    if (!$path) {
        $path = public_path('images/_mfx/images/imgdir_content/');
    }
    else{
        $path = ($path);
    }

    $folders = [];

    if (is_dir($path)) {
        // Alle Elemente im Verzeichnis (außer . und ..)
        $items = array_diff(scandir($path), ['.', '..']);

        foreach ($items as $item) {
            if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                $folders[] = $item;
            }
        }
    } else {
        logger("Verzeichnis existiert nicht: $path");
    }
    // dd($folders);
    return $folders;
}
}
if(!function_exists("readtitlez")){

    function readtitlez($path)
    {
        $file = @file_get_contents($path);
        $files = @json_decode($file);
        return $files;
    }
}
if(!function_exists("readimages_alt")){

    function readimages_alt($path)
    {
        $path = ($path);
        if (!File::isDirectory($path)) {
            logger("Verzeichnis existiert nicht: $path");
            return [];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allFiles = File::files($path);

        return collect($allFiles)
            ->filter(fn($file) => in_array(strtolower($file->getExtension()), $allowedExtensions))
            ->map(fn($file) => $path."/".$file->getFilename())
            ->toArray();

        // dd($files);
        //return $files;
    }
}
if(!function_exists("readimages")){

    function readimages($path)
    {
        $path = public_path($path);
        if (!File::isDirectory($path)) {
            logger("Verzeichnis existiert nicht: $path");
            return [];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allFiles = File::files($path);

        return collect($allFiles)
            ->filter(fn($file) => in_array(strtolower($file->getExtension()), $allowedExtensions))
            ->map(fn($file) => $file->getFilename())
            ->toArray();

        // dd($files);
        //return $files;
    }
}
if(!function_exists("CleanTable")){

    function CleanTable()
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // z.B. /admin/tables/show/Example
    $segments = explode('/', $path);

    $fo_vals = ['admin', 'tables', 'edit', 'delete', 'create', 'show', 'search', 'home', 'pictures'];

    // Durchlaufe die Segmente und filtere unerwünschte Werte
    $cleaned = array_filter($segments, function ($segment) use ($fo_vals) {
        return $segment !== '' && !in_array($segment, $fo_vals) && !is_numeric($segment);
    });

    // Zusammensetzen und Sonderzeichen entfernen
    $result = implode('', $cleaned);
    $result = str_replace(['[', ']', "'"], '', $result);

    return $result;
}

}
if(!function_exists('rinfo_code2'))
{
    function rinfo_code2($txt)
    {
            global $ln,$pref,$cfg;
        if(!defined("_frutel_new"))
        {
            define("_frutel_new",null);
        }
        $ln = 'de';
         $sd  = SD() ;  //== "hm" ? "_hm" : '';
        include resource_path("cfgz/rinfo/config_laravel".@$sd.".inc.php");

        include resource_path("cfgz/rinfo/config_general.inc.php");

        if(@$_SESSION[$pref."_dev_mod"])
        {
            $fs = "<font color='#a00'>";
            $fe = "</font>";
        }
        if($ln == "de" || @$cfg['xis_rinfo_lang_de'])
        {

            foreach($cf['snipp'] as $key=>$val)
            {
                $o = str_replace("|",'',$key);
                @$orige = @$cf['search_term'][$o];
                $ex = (@$_SESSION[$pref."_dev_mod"] ? " ($key)($orige)" : '');
                $txt = str_replace($key,@$fs.$val.$ex.@$fe,$txt);
            }
        }
    $txt = str_replace("fx_gen_cont_info()",gen_cont_info(),$txt);
    return $txt;

    }
}
if(!function_exists('smilies_oxx'))
{
   function smilies_oxx($str)
   {
        $str  = str_replace(array("Â§",'�?“',"�?","Ö"),array("&sect;","&Ouml;","ß","ß",'Ö',"&Ouml;"),$str);
        ///$str = str_replace_alt("¶","§",$str);
        return $str;
   }
}
if(!function_exists("rumLaut"))
{
    function rumLaut($str)
    {
        $find = [
    '/---/u', '/ÃƒÅ“/u', '/ÃƒÂ¼/u', '/ÃƒÅ¸/u', '/Ãƒ\?/u', '/ÃƒÂ¤/u', '/â€™/u', '/Ã„/u',
    '/Ãœ/iu', '/Ã/u', '/Ã¶/u', '/Ã"Y/u', '/Ã¼/iu', '/Ã¤/u', '/ÃŸ/iu', '/âEUR¦/u',
    '/ÃƒÂ¶/u', '/Â§/iu', '/Â©/iu', '/Ã/u'
];

$replace = [
    '<hr>', 'Ü', 'ü', 'ß', 'ß', 'ä', "'", 'Ä',
    'Ü', 'ß', 'ö', 'Ü', 'ü', 'ä', 'ß', '…',
    'ö', '§', '©', 'ß'
];

// alle Ersetzungen ausführen
$str = preg_replace($find, $replace, $str);
return $str;
    }
}



if(!function_exists("gen_hidemail"))
{
function gen_hidemail($ad)
{
    if(empty($ad))
    {
        return;
    }
$ad2 = str_replace("@","[at]",$ad);
 return "<a class='l".@$_GET['page']."' href='mailto:$ad'>". htmlentities($ad2) ."</a>";
}
}
if(!function_exists("gen_cont_info"))
{
function gen_cont_info($dom2='')
{
global $lang,$dbgg,$pref;

$dom = "ab";

$row = DB::table('kontaktdaten')
                  ->where('dom', $dom)
                  ->first();
$row = (array) $row;
$name = @$row['name'];
$adr = @$row['strasse'];
$plz = @$row['plz'];
$ort = @$row['ort'];
$region = @$row['bundesland'];
$tel = @$row['festnetz'];
$mobil = @$row['mobil'];
$fn = $tel ? "Telefon: <a href='tel:".$tel."'>$tel</a><br />" : '';
$mob  = $mobil ? 'Mobil'.": <a href='tel:".$mobil."'>$mobil</a><br />" : '';
$email = @gen_hidemail(@$row['email']);
$fax = (@$row['fax'] ? 'Fax'.": ".$row['fax']."<br />" : '');
return smilies_oxx("<div class=\"vcard\">
        <address><br />
        <div class='subheader'>
         <span class=\"fn\">$name</span><br />
         <span class=\"adr\">$adr<br />
         <span class='postalCode'>$plz</span> <span class=\"locality\">$ort</span><br />
         ".@$fn."
         $mob
         $fax
         Email: $email<br /><br />
         </div>
         </address>
         </div>");

}
}
if(!function_exists("SD"))
{
    // function SD($pn='')
    // {
    //     $subb = explode('.', str_replace("www.",'',request()->getHost()))[0];
    //     $pm = ["ab"=>"Asarios Blog",
    //            "dag"=>"Monika Dargies",
    //             "mfx"=>"MarbleFX",
    //             "mjs"=>"Mitja Schult",
    //             "chh"=>"Rechtsanwalt Christian Henning"];

    //     switch($subb){
    //         case "asario":
    //             $subb = "ab";

    //         break;
    //         case "monikadargies":
    //             $subb = "dag";

    //         break;
    //         case "marblefx":
    //             $subb = "mfx";

    //         break;
    //         case "mjs":
    //             $subb = "mjs";

    //         break;
    //         case "ra-c-henning":
    //             $subb = "chh";

    //         break;
    //         default:
    //         $subb = $subb;
    //         break;
    //     }
    //     if( substr_count(@$_SERVER['REQUEST_URI'],"/admin/"))
    //     {
    //       $subb2  = "ab";
    //         // dd($subb);
    //         return 'ab';
    //     }
    //     if(empty($subb))
    //     {
    //         $subb = "ab";
    //     }
    //     if(!$pn){
    //         return $subb;
    //     }
    //     elseif($pn == "pn"){
    //         return $pm[$subb];
    //     }
    //     return $subb2;

    // // Den Host in Teile aufteilen (subdomain.localhost.de -> ['subdomain', 'localhost', 'de'])


    // }
    function SD($pn='',$subb=''){

        if(!$subb)
        {
            $subb = explode('.', str_replace("www.",'',request()->getHost()))[0];
        }


        $pm = ["ab"=>"Asarios Blog",
               "dag"=>"Monika Dargies",
                "mfx"=>"MarbleFX",
                "mjs"=>"Mitja Schult",
                "chh"=>"Rechtsanwalt Christian Henning",
                "mail"=>"MCSL Mail Service"];
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
            case "localhost":
            $subb = "ab";
            break;
            case "test.mcs":
            $subb = "ab";
            break;
            case "241":
                $subb = "ab";
            break;
            case "217":
                $subb = "ab";
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
        return @$pm[$subb];
    // Den Host in Teile aufteilen (subdomain.localhost.de -> ['subdomain', 'localhost', 'de'])


    }
}
if(!function_exists('changeDatetimeToTimestamp'))
{
    function changeDatetimeToTimestamp($databaseName='laravel_tutorial')
    {
        $host = request()->getHost();
        $subdomain = SD(); // Nehme den ersten Teil als Subdomain
        if($subdomain != "" && $subdomain != 'ab')
        {
            $databaseName .= "_".$subdomain;
        }
    // Hole alle Tabellen aus der Datenbank
    $tables = DB::select("SHOW TABLES");
    $tableKey = 'Tables_in_' . $databaseName;

    foreach ($tables as $table)
    {
        $tableName = $table->$tableKey;

        if($tableName == "cache")
                {
                    continue;
                }
        // Hole alle Spalten für jede Tabelle
        $columns = DB::select("SHOW COLUMNS FROM {$tableName}");

        foreach ($columns as $column) {
            // Prüfe, ob der Datentyp der Spalte 'datetime' ist
            if (strpos($column->Type, 'timestamp') !== false) {
                $columnName = $column->Field;

                // 1. Konvertiere die 'DATETIME'-Werte zu Unix-Timestamps
                // $updateSql = "
                //     UPDATE {$tableName}
                //     SET {$columnName} = UNIX_TIMESTAMP({$columnName})
                //     WHERE {$columnName} IS NULL";
                //               // 2. Führe die SQL-Anweisung aus, um die Werte zu konvertieren
                // DB::statement($updateSql);
                $userx = DB::table($tableName)->select($columnName, 'id')->get();
                foreach ($userx as $user)
                {
                    $oldcol = $user->$columnName;
                    $newcol[$user->id] = strtotime($oldcol) ? strtotime($oldcol) : '0';
                    $ids[] = $user->id;

                    DB::table($tableName)
                        ->where('id', $user->id) // Setze hier die Bedingung
                        ->update([$columnName => NULL]);
                }
                DB::statement("ALTER TABLE $tableName MODIFY $columnName INT(10)");
                //echo "Spalte {$columnName} in Tabelle {$tableName} wurde zu Unix-Timestamp konvertiert.\n <br />";

            }

        }

        if(@$ids)
        {
            foreach($ids as $id)
            {
                DB::update("update {$tableName} set {$columnName} = '".$newcol[$id]."' WHERE id='$id'");
            }
        }


    }

}


}
if(!function_exists("checkurl"))
{
    function checkurl($t,$a='')
    {
        $fullUri = @$_SERVER['REQUEST_URI'];

        // Entfernt den Query-String, um nur den Pfad zu erhalten
        $path = parse_url($fullUri, PHP_URL_PATH);

        if($path == "admin/dashboard")
        {
            echo $a;
        }
    }
}
if(!function_exists("CheckRights"))
{
    function CheckRights($userId, $table, $right)
    {
        $fullUri = @$_SERVER['REQUEST_URI'];

        // Entfernt den Query-String, um nur den Pfad zu erhalten
        $path = parse_url($fullUri, PHP_URL_PATH);
        $ps = explode("/",$path);
        $host = $ps[1];
        // dd($host);
        if(@$_SESSION['Devm'] && ($host != "tables" && $host != "admin"))
        {
            return;
        }
        GlobalController::SetDomain();
        // Hole die user_rights_id des Nutzers aus der Tabelle users
        $user = DB::table('users')
            ->where('id', $userId)
            ->select('users_rights_id')
            ->first();
        $tableId = getPositionOfTable($table);


        // Überprüfe, ob der Benutzer existiert
        if (!$user) {

            return 0; // Benutzer nicht gefunden
        }
        if(!Auth::check())
        {
            return 0;
        }
        // Hole die Rechte des Nutzers aus der Tabelle users_rights
        $userRights = DB::table('users_rights')
            ->where('id', $user->users_rights_id)
            ->select('view_table',"add_table", 'edit_table', 'publish_table', 'delete_table',"date_table")
            ->first();

        // Überprüfe, ob die Rechte gefunden wurden
        if (!$userRights) {
            return 0; // Rechte nicht gefunden
        }

        // Überprüfe, ob die tableId innerhalb des Bit-Strings liegt
        if ($tableId < 0     || $tableId >= strlen($userRights->view_table)) {
            return 0; // Ungültige tableId
        }
        // Bestimme das Recht, das wir überprüfen wollen
        switch ($right) {
            case 'view':
                return @$userRights->view_table[$tableId] == '1' ? 1 : 0;
            case 'add':
                return @$userRights->add_table[$tableId] == '1' ? 1 : 0;
            case 'edit':
                return @$userRights->edit_table[$tableId] == '1' ? 1 : 0;
            case 'publish':
                return @$userRights->publish_table[$tableId] == '1' ? 1 : 0;
            case 'delete':
                return @$userRights->delete_table[$tableId] == '1' ? 1 : 0;
            case 'date':
                return @$userRights->date_table[$tableId] == '1' ? 1 : 0;
            default:
                return 0; // Ungültiger Rechtstyp
        }

}
}
if(!function_exists("AutoInc"))
{
    function AutoInc($table)
    {
        $tableName = $table; // Beispiel
        $nextAutoIncrement = DB::select("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND name = ?", [$tableName]);

        if (!empty($nextAutoIncrement)) {
            return $nextAutoIncrement[0]->AUTO_INCREMENT;
        }
        else
        {
            return;
        }
    }
}
if(!function_exists("shariff"))
{
    function shariff($post,$temps=array(),$table='blog_posts')
    {
        if($table == "pictures")
        {
        $cat = DB::table('image_categories')->where("id",$post->image_categories_id)->select('shortname')->first();
            $table .= "/".@$cat->shortname;
        }
        $post->title = $post->title ?? $post->headline;
        return "
        <div class=\"shariff\"
            data-backend-url=\"/shariff-backend\"
            data-button-style=\"icon\"
            data-services='[ \"facebook\",\"telegram\",\"whatsapp\", \"twitter\",\"xing\", \"info\"]'
            data-url=\"".route('posts.show', ["id"=>$post->id,"table"=>$table])."\"
            data-title=\"{$post->title}\">
        </div>
       <style>
       .shariff-button.info{
        border:none;

       }
       a:has(.fa-info) {
  display: none !important;
}

       </style>";
    }
}
if(!function_exists("shariff_old"))
{
    function shariff_old()
    {
        return '
    <dl class="layout-hint">

        </dl>
        <article class="slim">
        <header_alt>
    <div class="shariff" data-services="[&quot;facebook&quot;,&quot;twitter&quot;,&quot;telegram&quot;,&quot;xing&quot;,&quot;info&quot;]" data-button-style="icon"></div>
    </header>
    </article>';

    }


}
if(!function_exists("ConvertTypes"))
{
    function ConvertTypes()
    {

    $images = DB::table('images')->get();

    foreach ($images as $image) {
        if (!$image->image_categories_id) {
            continue; // überspringe, wenn keine ID vorhanden
        }

        $cat = DB::table('image_categories')
                ->where('id', $image->image_categories_id)
                ->first();

        if ($cat) {
            DB::table('images')
              ->where('id', $image->id)
              ->update([
                  'type_id' => $cat->type_id,
                  'categorie_id' => $cat->categorie_id,
              ]);
        }
    }

    echo "Images-Tabelle erfolgreich aktualisiert.\n";

    }
}

// if (!function_exists('decval')) {
//     function decval($data)
//     {
//         if ($data === null) {
//             return null;
//         }

//         $enc = new EncryptController();

//         // 🔹 Strings behandeln
//         if (is_string($data)) {
//             $maxAttempts = 4; // falls mehrfach verschlüsselt
//             $attempt = 0;

//             while ($attempt < $maxAttempts) {
//                 $decrypted = $enc->decryptString($data);

//                 // Wenn nichts passiert → abbrechen
//                 if ($decrypted === $data) {
//                     break;
//                 }

//                 $data = $decrypted;
//                 $attempt++;
//             }

//             return strip_tags(trim($data));
//         }

//         // 🔹 Objekte behandeln (z. B. stdClass von DB)
//         if (is_object($data)) {
//             foreach ($data as $key => $val) {
//                 $data->$key = decval($val);
//             }
//             return $data;
//         }

//         // 🔹 Arrays behandeln
//         if (is_array($data)) {
//             foreach ($data as $key => $val) {
//                 $data[$key] = decval($val);
//             }
//             return $data;
//         }

//         // 🔹 Zahlen, bool usw. unverändert zurückgeben
//         return $data;
//     }


// }
// if(!function_exists("decval_old")){


//     function decval_old($data)
//     {
//         if (!$data) return $data;

//         $enc = new EncryptController(env('APP_HASHCODE', "SECRET"));

//         // Wenn es ein String ist
//         if (is_string($data)) {
//             // Mehrfach entschlüsseln
//             $maxAttempts = 4;
//             $attempt = 0;
//             while ($attempt < $maxAttempts) {
//                 $decrypted = $enc->decryptString($data);
//                 if ($decrypted === false) break;
//                 $data = $decrypted;
//                 $attempt++;
//             }

//             $data = strip_tags($data);
//             $data = trim($data);
//             return $data;
//         }

//         // Wenn es ein Objekt ist, rekursiv alle String-Felder entschlüsseln
//         if (is_object($data)) {
//             foreach ($data as $key => $val) {
//                 $data->$key = decval($val);
//             }
//             return $data;
//         }

//         // Wenn es ein Array ist, rekursiv alle Elemente bearbeiten
//         if (is_array($data)) {
//             foreach ($data as $key => $val) {
//                 $data[$key] = decval($val);
//             }
//             return $data;
//         }

//         return $data;
//     }

// }
if(!function_exists("CheckZRights"))
{
    function CheckZRights($right)
    {
        if(!Auth::check())
        {
           //
           //
           //  return redirect("login")->send();
        }
        if(!Schema::hasColumn("users_rights","xkis_".$right))
        {
            return false;
        }

    if(!Auth::id())
    {
        return false;
    }


        $user = DB::table("users")->where("id",Auth::id())->select("users_rights_id")->first();
        $rights = DB::table("users_rights")->where("id",$user->users_rights_id)->select("xkis_".$right)->first();
        // vde($rights);
        $xr = "xkis_".$right;
        if($rights->$xr == "1")
        {
            return true;
        }
        return false;
    }
}
if(!function_exists("RedRights"))
{
function RedRights($right)
{
    if(!CheckZRights($right))
    {
        // return redirect('/no-rights')->send();
    }
}
}
if(!function_exists("stripslashes_recursive"))
{
    function stripslashes_recursive($data) {
        if (is_array($data)) {
            return array_map('stripslashes_recursive', $data); // Rekursive Anwendung
        } else {
            return stripslashes($data); // Standardmäßige Anwendung von stripslashes auf den Wert
        }
    }
}

if(!function_exists("ucf"))
{
    function ucf($str)
    {
        $arr = explode("_",$str);

        if(!is_array($arr))
        {
            return ucfirst($str);
        }
        foreach($arr as $val)
        {
            $na[] = ucfirst($val);
        }
        return implode(" ",$na);

    }
}
if(!function_exists("nl2"))
{
    function nl2($str)
    {
        return html_entity_decode(html_entity_decode(nl2br($str)));
    }
}
if(!function_exists("getPositionOfTable"))
{
    function getPositionOfTable($tableName)
    {
        // Hole alle Einträge aus der admin_table (mit name und id)
        $tables = DB::table('admin_table')
                    ->select('id', 'position', 'name')
                    ->get();

        // Konvertiere zu einem Array, damit die Suche durchgeführt werden kann
        $tableNames = $tables->pluck('name');

        // Suche nach der Position des Tabellennamens
        $position = $tableNames->search($tableName);

        // Wenn der Tabellenname gefunden wurde, gib die Position zurück
        if ($position !== false) {
            // Hole den Eintrag, der zu dieser Position gehört
            $tableEntry = $tables->get($position);

            // Falls die Position im Eintrag vorhanden ist, gib sie zurück
            if (isset($tableEntry->position)) {
                // \Log::info("Position gefunden: " . $tableEntry->position);
                return $tableEntry->position; // Gib die Position zurück
            }
        }

        // Wenn der Tabellenname nicht gefunden wurde, hole die ID des Tabelleneintrags
        $tableEntry = $tables->firstWhere('name', $tableName);

        if ($tableEntry) {
            // Gib die ID zurück, wenn kein position-Feld existiert oder die Suche nach der Position fehlschlägt
            // \Log::info("Tabellenname nicht gefunden, ID: " . $tableEntry->id);
            return $tableEntry->position; // Gib die ID zurück
        }

        // Wenn die Tabelle weder in den Namen noch in den IDs existiert, gib null zurück
        //\Log::info("Tabellenname wurde nicht gefunden.");
        return null;
    }

    // function getPositionOfTable($tableName)
    // {
    //     // Hole alle Einträge aus der admin_table
    //     $tables = DB::table('admin_table')
    //         ->pluck('name'); // Angenommen, die Spalte mit dem Tabellennamen heißt 'name'

    //     // Finde die Position des gesuchten Tabellennamens
    //     $position = $tables->search($tableName);
    //     // vd($tableName,$position);
    //     // $position++ ;
    //     // Gibt die Position zurück oder null, wenn nicht gefunden
    //     return $position !== false ? $position : null;
    // }
}
if(!function_exists('ageofArticle'))
{
    function ageofArticle($time){
        return Carbon::createFromTimestamp($time)->diffForHumans();
        $seconds = (time()-@$time);
        $minutes = ceil($seconds/60);
        $hours = $minutes/60;
        $days = $hours/60;
        $weeks = $days/7;
        $months = $days/30;
        $years = $days/365;
        $temp = array('years','months','weeks','days','hours','minutes','seconds');
        $temp_alt = array('Jahre','Monate','Wochen','Tage','stunden','minuten','sekunden');
        foreach($temp as $key => $val)
        {
            if($$val > 1)
            {
                $num = $$val;
                $tt = ucfirst($temp_alt[$key]);
                return ceil($num)." ".$tt;
            }
        }
    }
}
if(!function_exists("BP"))
{
    function BP()
    {
        return;
        return "http://localhost:8000/";
    }
}
if(!function_exists("small_images"))
{
    function small_images($dir)
    {
        // Überprüfen, ob der Ausgangsordner existiert
        $sourceDir = $dir . '/big';
        $targetDir = $dir . '/sm';

        if (!is_dir($sourceDir)) {
            echo "Der Ordner $sourceDir existiert nicht.\n";
            return;
        }

        // Zielordner erstellen, falls nicht vorhanden
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Unterstützte Bildtypen
        $supportedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        // Alle Dateien im Quellordner durchsuchen
        $files = scandir($sourceDir);
        foreach ($files as $file) {
            $filePath = $sourceDir . '/' . $file;

            // Nur Dateien mit unterstützten Bildtypen bearbeiten
            if (is_file($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), $supportedTypes)) {
                // Bild laden
                $imageInfo = getimagesize($filePath);
                if (!$imageInfo) {
                    echo "Überspringe ungültige Bilddatei: $filePath\n";
                    continue;
                }

                list($width, $height) = $imageInfo;
                $aspectRatio = $height / $width;
                $newWidth = 35; // Zielbreite
                $newHeight = intval($newWidth * $aspectRatio);

                // Neues Bild erstellen
                switch ($imageInfo['mime']) {
                    case 'image/jpeg':
                        $sourceImage = imagecreatefromjpeg($filePath);
                        break;
                    case 'image/png':
                        $sourceImage = imagecreatefrompng($filePath);
                        break;
                    case 'image/gif':
                        $sourceImage = imagecreatefromgif($filePath);
                        break;
                    default:
                        echo "Nicht unterstütztes Bildformat: $filePath\n";
                        continue 2;
                }

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

                // Transparenz für PNG und GIF behandeln
                if ($imageInfo['mime'] === 'image/png' || $imageInfo['mime'] === 'image/gif') {
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);
                    $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                    imagefill($resizedImage, 0, 0, $transparent);
                }

                // Bild skalieren
                imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Zielpfad
                $targetPath = $targetDir . '/' . $file;

                // Bild speichern
                switch ($imageInfo['mime']) {
                    case 'image/jpeg':
                        imagejpeg($resizedImage, $targetPath, 90); // 90% Qualität
                        break;
                    case 'image/png':
                        imagepng($resizedImage, $targetPath);
                        break;
                    case 'image/gif':
                        imagegif($resizedImage, $targetPath);
                        break;
                }

                // Ressourcen freigeben
                imagedestroy($sourceImage);
                imagedestroy($resizedImage);

                echo "Bild skaliert und gespeichert: $targetPath\n";
            }
        }
    }

}
if(!function_exists("errlog"))
{
    function errlog()
    {


        $fname = "../storage/logs/laravel.log";
        $le = "modules/mod_SEE/errorsize.dat";

        $file = file_get_contents($fname);
        // $file = $this->Exline($file);
        $file = strip_tags($file,"<br><b>");
        $div = "------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------\n";

        $PST = $_POST;

        if(@$_GET['clear'] == "All")
        {
           file_put_contents($fname,$div);
           file_put_contents($le,1);
           Header("Location: exxon.php?exx=errlog");
        }

        //var_dump($f);
        if(@$PST['mline'] || @$PST['mline2'])
        {

           file_put_contents($fname,$file."\n".$div);
           file_put_contents($le,filesize($fname));
           $div = '';
           header("Location: ".BP() );
        }
    $file = strip_tags(file_get_contents($fname),"<br>");
    // $file = $this->Exline($file);
    ?>
    <html>
    <head>
    <title>PHP Error Log</title>
    <link rel="shortcut icon" href="_images/icons/errlog.png">
    <script src='common/jquery.js' type='text/javascript'></script>
    <script src='common/scripts.js' type='text/javascript'></script>
    <style>
    .bigbtn{
    height:3% !important;
    width:33%;
    padding:9px !important;
    padding-bottom:9px !important;
    }
    </style>
    </head>
    <body onload="ScrollBottom();">
    <a href='#bottom' style='background-color:#00ffff;padding:5px;color:#f00;border-radius:4px;border:1px solid #000;font-weight:bold;text-decoration:none;'>BOTTOM</a><br /><br />
    <?php
    echo str_replace("Europe/Berlin","",nl2br($file.$div));
    $go = "-2";

    if(!empty($PST))
    {
        $go = "-2";
    }
    echo "<form method='post' action='exxon.php?exx=errlog'>";
    echo "<input type='submit' name='mline' value='Check Content' class='bigbtn'> <input type='hidden' name='mline2' value='1'> <input type='button' class='bigbtn' value='Go Backwards' OnClick=\"history.go('$go');\"><input class='bigbtn' type='button' value='Clear History' onclick=\"location.href='".@$_SERVER['REQUEST_URI']."&clear=All'\">";
    echo "</form>";
    echo "<a name='bottom'></a>";

    ?>
    </body>
    </html>
    <?php
    }

}
if(!function_exists('updateBladeFiles'))
{


function updateBladeFiles($viewsPath, $publicJsPath, $publicCssPath)
{
    // Erstelle die Ordner für JS und CSS, falls sie nicht existieren
    if (!is_dir($publicJsPath)) mkdir($publicJsPath, 0755, true);
    if (!is_dir($publicCssPath)) mkdir($publicCssPath, 0755, true);

    // Durchsuche alle Blade-Dateien im Ordner
    foreach (glob("$viewsPath/*.blade.php") as $bladeFile) {
        $content = file_get_contents($bladeFile);
        // \Log::info("blf:".json_encode($bladeFile));
        // Prozess für JavaScript-Dateien
        $content = processFiles($content, $publicJsPath, 'js', '/<script[^>]*src=["\'](https?:\/\/[^"\']+)["\'][^>]*><\/script>/siU');

        // Prozess für CSS-Dateien
        $content = processFiles($content, $publicCssPath, 'css', '/<link[^>]*href=["\'](https?:\/\/[^"\']+\.css)["\'][^>]*>/siU');

        // Speichere die geänderte Blade-Datei
        file_put_contents($bladeFile, $content);
    }
}
}
if(!function_exists('processFiles'))
{
function processFiles($content, $publicPath, $type, $regex)
{
    return preg_replace_callback(
        $regex,
        function ($matches) use ($publicPath, $type) {
            $url = $matches[1];
            $filename = basename(parse_url($url, PHP_URL_PATH));
            $localPath = "$publicPath/$filename";

            // Lade die Datei herunter und speichere sie im Zielverzeichnis
            if (!file_exists($localPath)) {
                //\Log::info("url: ".json_encode($url));
                file_put_contents($localPath, file_get_contents($url));
            }

            // Ersetze den externen Pfad durch einen `asset()`-Pfad
            $assetPath = "{{ asset('$type/$filename') }}";
            return $type === 'js' ? "<script src=\"$assetPath\"></script>" : "<link href=\"$assetPath\" rel=\"stylesheet\">";
        },
        $content
    );
}
}


