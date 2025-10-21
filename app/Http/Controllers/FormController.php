<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Author; // Author Modell, das die Autoren enthält
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use app\Http\Controllers\GlobalController;
use app\Http\Controllers\FormController;
use App\Services\Inkrementierer;
use App\Models\Settings;
use App\Models\Form;
use Filament\Forms\Form as Forma;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Collective\Html\FormFacade;

class FormController extends Controller
{
    public function __construct()
    {
       GlobalController::SetDomain();


    }
    public function br2ln($txt)
    {
        return nl2br($txt);
    }
    public static function generateUniqueFolder($basePath = 'uploads/') {
        do {
            $value = substr(md5(microtime(true) . random_int(0, PHP_INT_MAX)), 0, 6);
            $fullPath = $basePath . $value;
        } while (is_dir($fullPath));

        // Ordner anlegen, wenn gewünscht
        // mkdir($fullPath, 0755, true);

        return $value;
    }
    //VUE Formmaker
    public static function Fields($name,$value,$table,$id='',$create='')
    {
        if(!in_array($name,Settings::$excl_cols))
        {

            if($create && $name == "image_path"){
                $value = "008.jpg";
            }
            elseif($create && !substr_count($name,"_at") && $name != "blog_date")
            {
                $value = NULL;
            }
            elseif((substr_count($name,"_at") || $name == "blog_date") && !empty($create))
            {
                $value = now();
                // \Log::info("VAL: $name :".$value);
            }
            elseif((substr_count($name,"_at") || $name == "blog_date"))
            {
               // $value = $value;
            }
            if($name == "password" && !FormController::CheckCreate()){
                $value = "XXXXXXXXXX";
            }
            $value = html_entity_decode(nl2br($value));
            $value = str_replace(
                '<br /><br /><br /><br />',
                '<br /><br /><br />',
                $value
            );
            if(!empty($create) && substr_count($name,"imgdir_") && empty($_SESSION['asd']))
            {
                $bpath = "_".SD()."/images/".$name."/";
                $value = FormController::generateUniqueFolder("/images/".$bpath);
                $value2 = public_path("/images/".$bpath.$value);
                \Log::info($value2);
                mkdir($value2);
                mkdir($value2."/orig/");
                mkdir($value2."/big/");
                mkdir($value2."/sm/");
                mkdir($value2."/thumbs/");
                mkdir($value2."/ret/");
                mkdir($value2."/bthumbs/");
                $_SESSION['asd'] = basename($value2);
            }
            elseif(!empty($create) && substr_count($name,"imgdir_") && $_SESSION['asd'] ){
                $value = $_SESSION['asd'];
            }
            $value = str_replace("&nbsp;",'',$value);
            $value = decval($value);
            $value = rumlaut($value);
            $value = html_entity_decode($value);

        $label = isset(Settings::$exl[$name]) ? Settings::$exl[$name] : ucf($name);
        $class = FormController::getClass($name,1,$table);
        $req   = FormController::getReq($name);
        $type = FormController::getClass($name);
        $rows = FormController::getRows($type);
        $disa = FormController::getDisa($name,$table);
            $namefield = $name."Field";




            $fields[$name] = [
                'name'  => $name,
                'type'  => $type,
                'label' => $label,
                'value' => $value,
                'id'    => $id,
                'class' => $class,
                'rows'  => $rows,
                "required" => $req,
                "disabled" => $disa,

                ];
            if($name == "users_id")
            {
                $fields[$name]['users_id'] = Auth::id();
                // \Log::info("AUTHID:" . Auth::id());
            }
        // $fields[$namefield] = [
        //     'name'  => "$name",
        //     'type'  => "$type",
        //     'label' => "$label",
        //     'value' => "$value",
        //     'id'    => "$id",
        //     'class' => "$class",
        //     'rows' => "$rows",
        // ];
        if(!empty($sortop)){
            // \Log::info("Options: " . json_encode($fields, JSON_PRETTY_PRINT));

        }
        return $fields;

        }
    }
    public static function getDisa($name,$table)
    {
            if(isset($disa[$table])){

                if(!in_array($name,Settings::$disa[$table]) || CheckZRights("CommentsEdit"))
                {
                    return ;
                }
            }
            if($table != "comments")
            {
                return ;
            }


            return "disabled";

    }
    public static function getReq($oname)
    {
        $req = Settings::$no_req;
        if(!in_array($oname,$req))
        {
            return "required";
        }
        return;
    }
    public static function getRows($type,$cl=''){
        if($type == "textarea_short")
        {
            return "4";
        }
        return "10";
    }
    public static function getOptions($name)
{

    if (!substr_count($name, "_id")) {
        return [];
    }


    $table = str_replace("_id", '', $name);
    if (!Schema::hasTable($table)) {
        return [];
    }

    // Bestimmen, welche ID-Spalte verwendet werden soll
    $id = 'id'; // Standardmäßig 'id'
    if (Schema::hasColumn($table, 'position')) {
    $id2 = "position";
    }
    else{
        $id2 = "name";
    }
    // \Log::info("tab: ".$table);
    // Abfrage vorbereiten, abhängig von der Spalte 'pub'
    if (Schema::hasColumn($table, 'pub')) {
        $query = DB::table($table)->where("pub", "=", 1)->orderBy($id2, "ASC");  // Nach 'name' sortieren
    }
    else{
        $query = DB::table($table)->orderBy("name", "ASC");  // Nach 'name' sortieren
    }

    // Hole nur die 'id' und 'name' Felder
    $tabs = $query->select("id", "name")->get();
    if($table != "users"){
        $tabs = $query->select("id", "name")->get()->map(function ($tab) {
            $tab->name = ucf($tab->name);
            return $tab;
        });
    }
    else{
        $tabs = $query->where("xis_disabled","0")->get()->map(function ($tab) {
            $tab->name = ucf($tab->name);
            return $tab;
        });
    }

    $tabs = $tabs->sortBy('name');
    return $tabs;
}



    // public static function getOptions($name)
    // {
    //     if(!substr_count($name,"_id"))
    //     {
    //         return [];
    //     }
    //     $table = str_replace("_id",'',$name);
    //     if (Schema::hasColumn($table, 'possition')) {
    //     $id = 'position';
    //     }
    //     else{$id = "id";}
    //     if (Schema::hasColumn($table, 'pub')) {
    //         $query = DB::table($table)->where("pub", "=", 1)->orderBy("name","ASC");
    //     } else {
    //         $query = DB::table($table)->orderBy("name","ASC");
    //     }
    //     // \Log::info($query->select($id, "name")->orderBy('name', 'ASC')->toSql());

    //     $tabs = $query->select($id, "name")->reorder()->orderBy("{$table}.name","ASC")->get();

    //     $result = [];

    //     foreach ($tabs as $item) {
    //         $result[$item->$id] = $item->name; // <- Hier '->' statt '[]' verwenden
    //     }
    //     asort($result);
    //     return $result;
    // }
    public static function getOptions_sel($table, $name)
    {
        if($name == "status"){
            $statusvals = [
                ["id" => "empty", "name" => "keine Angabe"],
                ["id" => "forsale", "name" => "Zu Verkaufen"],
                ["id" => "givenaway", "name" => "Verschenkt"],
                ["id" => "sold", "name" => "Verkauft"],
                ["id" => "unsaleable", "name" => "Unverkäuflich"],
                ["id" => "lost", "name" => "Verloren"],
                ["id" => "inwork", "name" => "In Arbeit"]
            ];
        }
        if($name == "itemscope"){
            $statusvals = [

                ["id" => "empty", "name" => " "],
                ["id" => "Digital Art", "name" => "Digitale Kunst"],
                ["id" => "Painting", "name" => "Gemalt"],
                ["id" => "Photograph", "name" => "Fotos"],
                ["id" => "Sculpture", "name" => "3D Objekte"],

            ];

        }
        if($name == "typ")
        {
            $statusvals = [
                ["id"=>"foto","name"=>"Foto"],
                ["id"=>"gfx","name"=>"Grafik"],
                ["id"=>"KI","name"=>"Made with AI"],
                ["id"=>"wallpaper","name"=>"Wallpaper"],

            ];

        }
        if($name == "Gruppe"){
            $statusvals =[
                            ["id"=>"Familie","name"=>"Familie"],
                            ["id"=>"Friends","name"=>"Freunde"],
                            ["id"=>"Kunden","name"=>"Kunden"],
                            ["id"=>"Offiziell","name"=>"Offiziell"],
                            ["id"=>"Projects","name"=>"Projekte"],
            ];

        }
        // Direkt als assoziatives Array aufbauen
        $result = $statusvals;
        \Log::info("RESULT:".json_encode($result));
        return $result;
    }
    public static function getOptions_itemscope($table, $name)
    {

        // Direkt als assoziatives Array aufbauen
        $result = $statusvals;

        return $result;
    }
    public static function getClass($name,$cl='',$table='')
    {
        if(substr_count($name,"xkis_"))
        {
            return "isbox";
        }
        elseif(Substr_count($name,"xis_"))
        {
            if ($cl) {
                return "xis";
            }
            return "checkbox";
        }
        elseif(substr_count($name,"img_"    ) && $name != 'img_x' && $name != "img_y")
        {
            return "imul";
        }
        elseif(substr_count($name,"imgdir_"))
        {
            return "imgal";
        }
        switch($name)
        {
            case "id":
                if($cl)
                {
                    return "disabled";
                }
                return "text";
            break;
            case "admin_table_id":
                return "select_id";
            break;
            case "about":
                return "textarea";
            break;
            case "about_en":
                return "textarea";
            break;
            case "answer":
                return "textarea";
            break;
            case "autoslug":
                return "autoSlug";
            break;
            case "birthday":
                return "date";
            break;
            case "blog_authors_id":
                return "select_id";
            break;
            case "blog_categories_id":
                return "select_id";
            break;
            case "blog_date":
                if($cl && !CheckRights(Auth::id(),$table,"date")){
                    return "disabled";
                }
                return "datetime";
            break;
            case "camera_id":
                return "select_id";
            break;
            case "categorie_id":
                return "artselect";
            break;
            case "content":
                if($table == "comments")
                {
                    return "textarea_nohtml";
                }
                return "textarea";
            break;
            case "content_en":
                return "textarea";
            break;
            case "created_at":
                if($cl && !CheckRights(Auth::id(),$table,"date")){
                    return "disabled";
                }
                return "datetime";
            break;
            case "date":
                if($cl && !CheckRights(Auth::id(),$table,"date")){
                    return "disabled";
                }
                return "datetime";
            break;
            case "description":
                return "textarea";
            break;
            case "description_en":
                return "textarea";
            break;
            case "Gruppe":
                return "select";
            break;
            case "image_categories_id":
                return "select_id";
            break;
            case "image_path":
                return "IID";
            break;
            case "img_x":
                return "hidden";
            break;
            case "img_y":
                return "hidden";
            break;
            case "is_admin":
                return "isbox";
            break;
            case "ischecked":
                return "isbox";
            break;
            case "is_customer":
                return "isbox";
            break;
            case "is_employee":
                return "isbox";
            break;
            case "itemscope":
                return "select";
            break;
            case "Kommentar":
                return "textarea";
            break;
            case "last_login_at":
                if($cl){
                    return "disabled";
                }
                return "datetime";
            break;
            case "markdown_on":
                if ($cl) {
                    return "xis";
                }
                return "checkbox";
            break;
            case "message":
                return "textarea";
            break;
            case "message_en":
                return "textarea";
            break;
            case "name":
                return "text";
            break;
            case "password":
                if($cl && !FormController::CheckCreate())
                {
                    return "disabled";
                }
                return "password";
            break;
            case "position":
                return "position";
            break;
            case "preis":
                return "price";
            break;
            case "profile_photo_path":
                if($cl){
                    return "profile";
                }
                return "IID";
            break;
            case "projects_id":
                return "select_id";
            break;
            case "pub":
                return "pub";
            break;
            case "reading_time":
                return "reading_time";
            break;
            case "status":
                return "select";
            break;
            case "story":
                return "textarea";
            break;
            case "story_en":
                return "textarea";
            break;
            case "summary":
                return "textarea";
            break;
            case "text":
                return "textarea";
            break;
            case "type_id":
                return "hidden";
            break;
            case "typ":
                return "select";
            break;
            case "us_poster":
                return "poster_id";
            break;
            case "users_id":
                return "select_id";
            break;
            case "xis_aiImage":
                if ($cl) {
                    return "xis";
                }
                return "checkbox";
            break;
            case "xis_disabled":
                return "isbox";
            break;
            case "xis_IsSaleable":
                if ($cl) {
                    return "xis";
                }
                return "checkbox";

            break;
            case "xis_mcs":
                if ($cl) {
                    return "xis";
                }
                return "checkbox";

            break;
            case "xis_mcsl":
                if ($cl) {
                    return "xis";
                }
                return "checkbox";

            break;
            case "xis_shopable":
                return "isbox";
            break;
            case "xkis_Ticker":
                if ($cl) {
                    return "xis";
                }
                return "checkbox";
            break;
            default:
                return "text";
            break;
        }

    }
    public static function CheckCreate(){
        if(!substr_count(@$_SERVER['REQUEST_URI'],"create")){
            return false;
        }
        return true;
    }
    // Methode, die das Formular anzeigt
    /**
     *
     * Formular im tablescontroller
     *
     */
    public static function selectFieldType($name,$value,$table,$create='',$id='')
    {
        switch($name)
        {
            case "content":
                return FormController::textArea($name,$value,$id);
            break;
            case "pub":
                return FormController::pubform($name,$value,$table,$id);
            break;
            case "message":
                return FormController::textArea($name,$value,$id);
            break;
            case "message_en":
                return FormController::textArea($name,$value,$id);
            break;
            case "content_en":
                return FormController::textArea($name,$value,$id);
            break;
            case "text":
                return FormController::textArea($name,$value,$id);
            break;
            case "id":
                return FormController::idField($name,$value);
            break;
            case "birthday":
                return FormController::BdayField($name,$value);
            break;
            case "image_path":
                return FormController::IM_Alt($name,$value,$table,$id,$create,'imul/uploadtable');
            break;
            case "image_path_alt":
                return FormController::IM_Alt($name,$value,$table,$id,$create,'profile');
            break;
            case "author_id":
                return  FormController::AuthorSelect($name,$value,$table,$id,"_alt");
            break;
            case "categories_id":
                return  FormController::IdSelect($name,$value,$table,$id,"_alt",'blog_categories');
            break;
            case "users_id":
                return  FormController::AuthorSelect($name,$value,$table,$id,"_alt");
            break;
            case "xkis_ai":
                return FormController::xis_box($name,$value,$table,$id);
            break;
            case "xis_ai":
                return FormController::xis_box($name,$value,$table,$id);
            break;
            case "xkis_Ticker":
                return FormController::xis_box($name,$value,$table,$id);
            break;
            case "xis_IsSaleable":
                return FormController::xis_box($name,$value,$table,$id);
            break;
            case "preis":
                return FormController::curr_box($name,$value,$table);
            break;
            case "status":
                return FormController::enum_box($name,$value,$table,$id);
            break;
            case "date_begin":
                $_SESSION['begdate'][$id] = $value;
                return;
            break;
            case "date_end":
                return "<br />".FormController::DateBegin($name,$value,$table,$id);
            break;

            case "moderator_id":
                return  FormController::AuthorSelect($name,$value,$table,$id);
            break;
            case "camera_id":
                return  FormController::ImSelect($name,$value,$table,"camera","name",$id,1);
            break;
            case "image_categories_id":
                return  FormController::ImSelect($name,$value,$table,"images_cat","shortname",$id,1);
            break;
            case "password":
                return  FormController::pass($name,$value);
            break;
            case "created_at":
                return FormController::date($name,$value,$table,'',$id);
            break;
            case "date":
                return FormController::date($name,$value,$table,'',$id);
            break;
            case "attempts":
                return FormController::Numbers($name,$value);
            default:
                return FormController::TField($name,$value);
            break;
        }
    }
    /**
     *
     * Inline Form
     *
     */
    public static function selectFieldText($name,$value,$table,$create='',$id,$uid='')
    {
        switch($name)
        {
            case "users_groups":
                return FormController::ugroup($name,$value,$table,$id,$uid);
            break;
            case "position":
                return "<td class='handle'>".FormController::Position($name,$value,$table)."</td>";
            break;
            case "image_path":
                return "<td class='table-cell image-column'>".FormController::IM_Alt($name,$value,$table,$id,"index",'imul/uploadcreate')."</td>";
            break;
            case "image_path_alt":
                return "<td class='table-cell image-column'>".FormController::IM_Alt($name,$value,$table,$id,$create,'profile')."</td>";
            break;
            case "date_begin":
                $_SESSION['begdate'][$id] = $value;
                return;
            break;
            case "date_end":
                return "<td class='table-cell date-begin'>".FormController::DateBegin($name,$value,$table,$id)."</td>";
            break;
            case "id":
                return ;
            break;
            case "categories_id":
                return "<td  class='selectable table-cell' data-field='".$name."'>".FormController::idselectinl($name,$value,"blog_categories",$id)."</td>";
            break;
            case "moderator_id":
                return "<td  class='selectable table-cell' data-field='".$name."'>".FormController::author($name,$value,$table,$id)."</td>";
            break;
            case "users_id":
                return  "<td class='selectable table-cell' data-field='".$name."'>".FormController::author($name,$value,$table,$id)."</td>";
            break;
            case "image_categories_id":
                return "<td  class='selectable table-cell' data-field='".$name."'>".FormController::uid($name,$value,$table,$id,"images_cat","name",$id)."</td>";
            break;
            case "camera_id":
                return "<td  class='selectable table-cell' data-field='".$name."'>".FormController::uid($name,$value,$table,$id,"camera","name",$id)."</td>";
            break;
            case "date":
                return "<td >".FormController::date($name,$value,$table,'',$id)."</td>";
            break;
            case "created_at":
                return "<td >".FormController::date($name,$value,$table,'',$id)."</td>";
            break;
            case "pub":
                return "<td>".FormController::publish($name,$value,$table,$id)."</td>";
            break;
            case "birthday":
                return "<td class='table-cell'>".FormController::age($name,$value,$table,$id)."</td>";
            break;
            case "xis_ai":
                return '<td class="editable table-cell" data-field="'.$name.'">'.FormController::xisi_box($name,$value,$table,$id).'</td>';
            break;
            case "status":
                return '<td class="table-cell selectable" data-field="'.$name.'">'.FormController::enum_box($name,$value,$table,$id,1).'</td>';
            break;
            case "author_id":
                return "<td class='selectable table-cell' data-field='".$name."'>".FormController::author($name,$value,$table,$id)."</td>";
            break;
            default:
                return  '<td class="editable table-cell" data-id="'.$id.'" data-column="'.$name.'" data-field="'.$name.'">'.FormController::mtext($name,$id,$value)."</td>";
            break;

        }
    }
    public static function IM_Alt($name,$value,$table,$id,$create,$action)
    {

        if($action == 'imul/uploadcreate')
        {
            $xtf = "<form method='post' action='$action' ectype='multipart/formdata' id='uploadForm_$id'>
                    <input type='hidden' name='_token' value='".csrf_token()."'>
                    <input type='hidden' name='table' value='$table'>";

            $xtfe = "</form>";

            $ad = "_$id";
            $ttl = "".$action;
        }
        elseif($action != "profile" && $action != 'imul/uploadtable')
        {
            $xtf = "<form method='post' action='$action' ectype='multipart/formdata' id='uploadForm_$id'>
                    <input type='hidden' name='_token' value='".csrf_token()."'>
                    <input type='hidden' name='table' value='$table'>";
            $xtfe = "</form>";
            $ad = "_$id";
            $ttl = "".$action;
        }
        $ad = "_$id";

        $_SESSION['ttf'] = @$ttl;
        $_SESSION['adid'] = @$ad;
        return '
        '.@$xtf.'
        <img src="/images/'.$table.'/thumbs/'.$value.'" alt="Preview Thumbnail" class="preview-thumb" id="preview-thumb_'.$id.'" style="cursor:pointer;" width="100" OnClick=\'$("#uploadModal_'.$id.'").show();\'/>

        <!-- Modal -->

        <div class="modal" id="uploadModal_'.$id.'" tabindex="-1" aria-labelledby="uploadModalLabel_'.$id.'">
        <div class="modal-dialog modal-dialog-   modal-fullscreen">
            <div class="modal-content">
            <!-- Modal Header -->


    <div class="modal-header d-flex justify-content-between align-items-center" style="min-width:100% !important;" align="left">
    <h3 class="modal-title" id="uploadModalLabel_'.$id.'">Upload File</h3>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>



            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Dropzone -->
                <div class="border border-dashed p-3 text-center dropzone" id="dropzone_'.$id.'">
                <lable for="fileInput_'.$id.'" style="font-weight:bold;cursor:pointer;" class="mb-2" onClick="document.getElementById(\'fileInput_'.$id.'\').click();"><p>Ziehen Sie Ihre Datei hierher</p>
                <p>oder durchsuchen , um Dateien auszuwählen</p></label>
                <input type="file" id="fileInput_'.$id.'" style="display: none;" name="image_path" multiple>
                <!-- <input type="hidden" id="fileInput_alt_'.$id.'" style="display: none;" name="image_path"> -->
                </div>

                <!-- Preview Thumbnail -->
                <div id="filePreview_'.$id.'" class="mt-3 text-center">
                <img id="uploadedThumb_'.$id.'" class="img-thumbnail uploadedThumb" style="display: none; max-width: 100px;">
                </div>
                <div id="progressContainer_'.$id.'" style="display:none;">
                    <label for="progressBar_'.$id.'">Upload Fortschritt:</label>
                    <progress id="progressBar_'.$id.'" value="0" max="100"></progress>
                    <span id="progressText_'.$id.'">0%</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="uploadButton_'.$id.'" class="uploadButton"
    data-id="'.$id.'"
    data-action="'.$action.'"
    data-ad="'.@$ad.'">Close</button>
                <button type="button" class="btn btn-primary" id="submitButton_'.$id.'" onClick="submitForm('.$id.')">Submit</button>
            </div>
            </div>
        </div>
        </div>
        '.@$xtfe.'
        <script>

document.addEventListener("DOMContentLoaded", function() {

var $j = jQuery.noConflict(); // Weist jQuery einer anderen Variable zu, um Konflikte zu vermeiden
      $j("#preview-thumb_'.$id.'").on("click", function() {

         $j("#uploadModal_'.$id.'").modal("show");  // Modal öffnen
    });
      $j("#closedown_'.$id.'").on("click", function() {
         $j("#uploadModal_'.$id.'").modal("hide");  // Modal schliessen
    });
      $j("#closeup_'.$id.'").on("click", function() {
         $j("#uploadModal_'.$id.'").modal("hide");  // Modal schliessen
    });
});
//         $k(document).on("click", ".uploadButton", function () {
//     alert("clicked");
//     const id = $k(this).data("id");
//     const action = $k(this).data("action");
//     const ad = $k(this).data("ad");

//     // Aufruf der Funktion mit den Daten
//     upload_filez(id, action, ad);
// });
   upload_filez('.$id.',"'.@$ttl.'","'.@$ad.'");
</script>';

    }
    public static function ugroup($name,$value,$table,$id,$uid)
    {


            $ugroup = DB::table($table)
        ->where('name', $value) // Replace $id with the actual user ID
        ->orderBy("name","ASC")
        ->select('name','shortname')->first(); // Get the value of the 'name' column
      //     return $nick;

        return "<nobr><span data-id='$id' data-column='$name'></span>".FormController::USelect($name,$value,'',$id,'',$table,$ugroup->shortname,$uid);
    }
    public static function USelect($name,$value,$hidden,$id,$alt='',$table,$sn,$uid)
    {
        $authors = DB::table($table)
            ->where('pub', '1')
            ->orderBy("position", 'ASC')
            ->get();

        $options = '';
        $selectedValue  = $value;

        foreach ($authors as $author) {
            if (!empty($selectedValue))
            {
                $selected = $selectedValue == $author->name ? 'selected' : '';
            }
            else
            {
                $selected = '';
            }

            $options .= "<option value='{$author->id}' {$selected}>{$author->name}</option>";
        }
        $alt  = $alt ? "_alt" : '';
    // \Log::info("uid:".$uid);
        return "<input type='hidden' id='f_".$id."' value='{$name}'><select class='form-control b_sel {$hidden} usel' data-uid='{$uid}' data-id='{$id}' data-column='{$name}' name='{$name}' id='usel-{$id}'>{$options}</select></nobr>";
    }
    public static function save_ugroup(Request $request)
{
    // Validierung der Request-Daten
    $validated = $request->validate([
        'uid' => 'required|integer',   // Benutzer-ID (User ID)
        'id' => 'required|integer',   // Gruppen-ID oder ein Referenzwert
        'value' => 'required|string', // Wert, der gespeichert werden soll
    ]);

    try {
        // Suche den User basierend auf der UID
        $user = User::findOrFail($validated['uid']); // Holt den Benutzer aus der Tabelle "users"

        // Update oder spezifisches Feld speichern
        $user->users_rights_id = $validated['value']; // Beispiel: Aktualisiert die Gruppen-ID
        $user->save(); // Speichert die Änderungen in der Datenbank

        // Erfolgsmeldung zurückgeben
        return response()->json(['success' => true, 'message' => 'Daten erfolgreich gespeichert.']);
    } catch (\Exception $e) {
        // Fehler behandeln
        return response()->json(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()], 500);
    }
}

    public static function idselectinl($name,$value,$table,$id)
    {
            $nick = DB::table($table)
        ->where('id', $value) // Replace $id with the actual user ID
        ->orderBy("name","ASC")
        ->value('name'); // Get the value of the 'name' column
      //     return $nick;
        return "<span data-id='$id' data-column='$name' class='sele'>".$nick."</span>".FormController::IdSelect($name,$value,'hidden2',$id,'',$table);
    }
    public static function author($name,$value,$table,$id)
    {
            $nick = DB::table('users')
        ->where('id', $value) // Replace $id with the actual user ID
        ->orderBy("name","ASC")
        ->value('name'); // Get the value of the 'name' column
      //     return $nick;
        return "<span data-id='$id' data-column='$name' class='sele'>".$nick."</span>".FormController::AuthorSelect($name,$value,'hidden2',$id);
    }
    public static function updateSelectable()
{
    // Hole die Daten aus der Anfrage
    $id = request('id');        // ID des Eintrags
    $table = request('table');  // Tabelle
    $name = request('name');    // Spaltenname
    $value = request('value');  // Neuer Wert
        // // Validierung der Eingabedaten
        // $validator = Validator::make(
        //     compact('id', 'table', 'name', 'value'),
        //     [
        //         'id' => 'required|integer|exists:' . $table . ',id', // ID muss existieren
        //         'table' => 'required|string|in:allowed_tables', // Tabelle muss erlaubt sein
        //         'name' => 'required|string|max:255',           // Spaltenname
        //         'value' => 'required|string|max:255',          // Neuer Wert
        //     ]
        // );

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $validator->errors(),
        //     ], 422); // Unprocessable Entity
        // }

    try {
        // Aktualisiere den Eintrag in der Datenbank
        DB::table($table)
            ->where('id', $id)
            ->update([$name => $value]);

        return response()->json([
            'status' => 'success',
            'message' => 'Daten erfolgreich aktualisiert',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Fehler beim Aktualisieren der Daten: ' . $e->getMessage(),
        ], 500);
    }
}

    public static function AuthorSelect($name, $selectedValue = '', $hidden = '',$id,$alt='')
    {
        // Setze die Subdomain-basierte Datenbankverbindung
        $subdomain = SD();

        if ($subdomain == 'hm') {
            // Wechsel zur 'laravel_tutorial_hm' Verbindung
            $connection = DB::connection('mariadb_mfx');  // Sicherstellen, dass diese Verbindung in config/database.php definiert ist
        } else {
            // Verwende die Standardverbindung
            $connection = DB::connection();  // Standardverbindung (laravel_tutorial)
        }

        // Führe die Abfrage auf der ausgewählten Verbindung aus
        $authors = $connection->table('users')->orderBy("name","ASC")->get();

        $options = '';

        foreach ($authors as $author) {
            if (!empty($selectedValue))
            {
                $selected = $selectedValue == $author->id ? 'selected' : '';
            }
            elseif($author->id == Auth::id())
            {
                $selected = 'selected';
            }
            else
            {
                $selected = '';
            }

            $options .= "<option value='{$author->id}' {$selected}>{$author->name}</option>";
        }

        return "<input type='hidden' id='f_".$id."' value='{$name}'><select name='{$name}' class='form-control a_sel{$alt} {$hidden}' data-id='{$id}' data-column='{$name}'>{$options}</select>";
    }
    public static function IdSelect($name, $selectedValue = '', $hidden = '',$id,$alt='',$table)
    {
        // Setze die Subdomain-basierte Datenbankverbindung
        $subdomain = SD();

        // Führe die Abfrage auf der ausgewählten Verbindung aus
        $authors = DB::table($table)->orderBy("position","ASC")->get();

        $options = '';

        foreach ($authors as $author) {
            if (!empty($selectedValue))
            {
                $selected = $selectedValue == $author->id ? 'selected' : '';
            }
            else
            {
                $selected = '';
            }

            $options .= "<option value='{$author->id}' {$selected}>{$author->name}</option>";
        }

        return "<input type='hidden' id='f_".$id."' value='{$name}'><select name='{$name}' class='form-control a_sel{$alt} {$hidden}' data-id='{$id}' data-column='{$name}'>{$options}</select>";
    }

    public function getEnumValuesFromColumn($table,$column)
    {
        // SQL-Abfrage, um die Tabelleneigenschaften (einschließlich Enum-Werte) abzurufen
        $result = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");

        // Hole die enum-Werte aus der 'Type' Spalte
        if (count($result) > 0) {
            $type = $result[0]->Type; // Die 'Type' Spalte enthält die Enum-Werte

            // Extrahiere die Enum-Werte aus dem Type-String
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $enumValues = explode(',', $matches[1]);

            // Entferne die Anführungszeichen aus den Werten
            $enumValues = array_map(function ($value) {
                return trim($value, "'");
            }, $enumValues);

            return $enumValues; // Gibt die Enum-Werte zurück
        }

        return []; // Falls die Spalte nicht existiert
    }
    public function updateStatus(Request $request, $table, $name, $id)
{
    // Validierung der Eingabedaten
    $enumValues = FormController::getEnumValuesFromColumn($table,$name); // Holt alle möglichen Enum-Werte

    // Validierung der Eingabedaten
    $request->validate([
        $name => ['required', 'in:' . implode(',', $enumValues)],
    ]);

    // Den Status aus dem Request holen
    $status = $request->input('status');

    // Update der Zeile in der Tabelle mit der angegebenen ID
    DB::table($table)
        ->where('id', $id) // Sucht die Zeile mit der angegebenen ID
        ->update([$name => $status]); // Aktualisiert den Status
    $updatedStatus = ucfirst($status);
    // Antwort zurückgeben (z. B. Erfolgsnachricht)
    return response()->json([
        'success' => true,
        'updatedStatus' => $updatedStatus, // Hier wird der neue Statuswert übergeben
    ]);
}
    public function updateStatus_old(Request $request)
    {
       $request->validate([
            'status' => 'required|in:empty,sold,forsale,unsaleable,givenaway,lost,inwork', // Passe die Werte an deine Enums an
        ]);


        // Beispiel: Speichere die Änderung für ein Produkt
       DB::table($table)- $request->input('status');


        return response()->json([
            'success' => true,
            'updatedStatus' => ucfirst($product->status),
        ]);
    }
    public static function xis_box($n,$v,$t,$id,$su='return false;')
    {
        $ch = $v == 1 ? "checked" : '';
        return "<div class='form-check form-switch'>
                <label class='switch'>
                    <br /><input type='hidden' name='$n' id='{$n}' value='0'>
                                <input type='checkbox' class='big-checkbox' OnChange=\"{$su}\" $ch name='$n' id='{$n}_{$id}' value='1'>
                    <span class='slider'></span>
                </label>
                </div>";
    }
    public static function enum_box($n,$v,$t,$id,$a='')
    {
        return  FormController::showForm($t,$n,$v,$id,$a);
    }

    public static function showForm($table,$name,$value,$id,$a)
    {
        // Enum-Werte aus der Tabelle 'products', Spalte 'status'
        $enumValues = FormController::getEnumValues($table, $name);
        $view = $a ? "enum_inline" : "enum_form";
        return view("forms.".$view, compact('enumValues',"name","table","value","id"));
    }

    public static function getEnumValues(string $table, string $column): array
    {
        $type = DB::selectOne("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column])->Type;
        preg_match('/enum\((.*)\)$/', $type, $matches);
        $enumValues = str_getcsv($matches[1], ',', "'");
        return $enumValues;
    }
    public function updateText(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'column' => 'required|string',
            'table' => 'required|string',
            'value' => 'required|string|max:10000', // Optional: max-Länge anpassen
        ]);
        $res = DB::table($validated['table'])
            ->where('id', $validated['id'])
            ->update([$validated['column'] => $validated['value']]);
        if($res)
        {
            // \Log::info("res:".json_encode($res,$validated['id']));
            return response()->json(['success' => true]);
        }

        if($res){

        }

        // return response()->json(['success' => false], 404);
    }
    public static function xisi_box($n,$v,$t,$id)
    {
        $su = "upd_xis('$n','$v','$t','$id');";
        return FormController::xis_box($n,$v,$t,$id,$su);
    }
    public static function curr_box($n,$v,$t)
    {
        return "<input type='text' size='7' name='$n' id='$n' value='$v'>&euro;";
    }
    public static function Numbers($n,$v)
    {
        return "<input type='number' name='$n' value='21' class='form-control'>";

    }
    public static function mtext($name,$id,$value)
    {
        $v3 = shorter($value);

        $xx = "<textarea style='display:none;' id='text_hidden_".$name."_".$id."' class='full-text' rows='4' cols='65'>$value</textarea>";
        return $xx."<span class='short-text'>".smilies(strip_tags(stripcslashes($v3)))."</span>";
    }
    public static function age($n,$v,$t,$id)
    {
        $timestamp = $v; // Beispielgeburtsdatum im Format dd.mm.yyyy
        $age = $timestamp ? Carbon::createFromTimestamp($timestamp)->age : "N/A";
        // \Log::info(json_encode($age."v:".$v));
        if(empty($v))
        {
            $v = 0;
        }
        return '<div class="age-wrapper" id="w_'.$id.'" style="display:none;">
                   <nobr> <input data-id="'.$id.'" data-table="'.$t.'" data-column="'.$n.'"  class="age datetimepicker2 datetime-input_alt" id="age_'.$id.'" name="'.$n.'" type="date" value="'.$v.'" maxlength="12" size="12">

                    <span class="log">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                    </span>
                    </nobr>
                 </div>
                 <div class="preview" id="pfield_'.$id.'">'.$age.'</div>'.FormController::AgeScript($n,$v,$t,$id);

    }
    public static function AgeScript($n,$v,$t,$id)
    {
        return "<script>
        age_form($id,'$n','$v','$t');
</script>";

    }
    public static function publish($n,$v,$t,$id)
    {
        if(!CheckRights(Auth::id(),$t,"publish"))
        {
            $v = "2";
            $dis = "disabled";
            $alt = "Sie haben nicht die benötigten Rechte";
            $v2 = '2';
        }
        elseif($v == "1")
        {
            $alt = "Veröffentlicht";
            $v2 = 0;

        }
        else
        {
            $v2 = 1;

            $alt = "Nicht Veröffentlicht";
        }
        return "<form method='post' action='/tables/subpub/$id' id='subpub_$id' style='display:inline;'>
                <input type='hidden' name='_token' value='".csrf_token()."'>
                <input type='hidden' name='pub' value='$v2'>
                <input type='hidden' name='table' value='$t'>
                <input type='hidden' name='id' value='$id'>
                <button ".@$dis." OnClick=\"document.getElementById('subpub_$id').submit();\" class='btn btn-outline-secondary' alt='$alt' title='$alt'><img src='".asset("images/icons/pub$v.png")."' class='table_thumb'/></button>

                </form>";
    }
    public static function pubform($n,$v,$t,$id)
    {
        $st = DB::table($t)->where('id',$id)->select('pub')->first();
        $sel = '';
        $lastSegment = request()->segment(count(request()->segments()));
        if(empty($st->pub) && $lastSegment != "create")
        {
            $sel0 = "checked";
        }
        else
        {
            $sel1 = "checked";
        }
        return "<br />
        <input type='radio' name='pub' id='pub1' value='1' ".@$sel1."><label for='pub1'>&nbsp;<img src='/images/icons/pub1.png'>&nbsp;Öffentlich</label>&nbsp;&nbsp;
        <input type='radio' name='pub' id='pub0' value='0' ".@$sel0."><label for='pub0'>&nbsp;<img src='/images/icons/pub0.png'>&nbsp;Nicht Öffentlich</label>
        <textarea class='thid' name='dummy' id='dummy_$id' cols='95' rows='0'></textarea>";

    }
     public static function Position($n,$v,$t)
    {
        return "<button class='btn btn-secondary hov_yel'><i class='fas fa-arrows-alt'></i></button>";
    }
    public function dform()
    {

        // Initialize the form with the model instance
    //    $form = Forma::make($this);


        // Add components to the form
        $ff =
            DateTimePicker::make('created_at')
                ->label('Datum und Uhrzeit')
                ->required()
                ->default(now())
                ->format('d.m.Y H:i:s');


        return view('forms.datetime', compact('ff'));
    }
    public static function DateBegin($name,$value,$table,$id)
    {
        $value2 = @$_SESSION['begdate'][$id];
        return FormController::date_range($name,$value,$table,'',$id,'inf','inf2',$value2);
    }
    public static function date_range($n,$v,$t,$w='',$id,$inf='',$inf2='',$v1='0')
    {
        $disp = empty($v) ? "block" : 'none';
        //\Log::info("v1:".$v1);
        $tt = empty($v) ? 0 : $v;
        $v1 = empty($v1) ? now() : $v1;
        $disp2 = empty($v) ? "none" : 'block';
        $dm = $_SESSION['dm'] ?? "dark";
        return "<div class='datetime-wrapper' id='dtw'>
    <!-- Erster Datetimepicker -->
        <nobr><span style='border-radius:4px;'>Von: <input type='datetime-local' id='datetimepicker1_".$id."' value='".$v1."' name='date_begin' class=' xtop datetimepicker3 datetime-input'>
        <i class='fa-regular fa-calendar datetime-icon' style='position:relative;right:25px;top:-0px;'></i></span></nobr>
        <br />
    <!-- Wrapper für zweiten Datetimepicker -->
        <div id='graphicWrapper' class='datetime-wrapper'  style='position: relative; display: inline-block;'>
    <!-- Grafik, die den zweiten Datetimepicker verdeckt -->
        <img src='/images/icons/datetime_inf_".$dm.".png'
            class='dminf'
            id='graphicOverlay".$id."'
             alt='Unendlich Grafik'
             onCLick=\"\$k('#datetimepicker2_".$id."').focus();\"
             style='cursor: normal; position: absolute; top: 7px; left: 0; z-index: 2; display: ".$disp.";'>
    <!-- Zweiter Datetimepicker -->

        Bis: <img style='display:inline;' src='/images/spacer.gif' height='1' width='5'><input type='datetime-local' value='".$tt."' id='datetimepicker2_".$id."' name='date_end' OnChange='dpicker_hide($id);return false;' class='datetimepicker3 xbot datetime-input' style='z-index: 1;'>
        <input type='time' id='time-fallback' style='display: none;'>
        <i class='fa-regular fa-calendar datetime-icon2'></i>
    </div>
        <br />
    <!-- Unendlich Button -->
    <input type='button' class='btn btn-default btn-light2 btn-sm' id='unlimitedBtn".$id."' style='display: ".$disp2."; margin-top: 10px;' value='Unendlich'>
</div>
        <script>
        dpicker_hide('".$id."','".$v."')
        </script>

    </script>";
    $XX = "
    <script>
    function gdate(id)
    {
    var startDate,
        endDate,
        updateStartDate = function() {
            startPicker.setStartRange(startDate);
            endPicker.setStartRange(startDate);
            endPicker.setMinDate(startDate);
        },
        updateEndDate = function() {
            startPicker.setEndRange(endDate);
            startPicker.setMaxDate(endDate);
            endPicker.setEndRange(endDate);
        },
        startPicker = new Pikaday({
            field: document.getElementById('datetimepicker1_".$id."'),
            minDate: new Date(),
            maxDate: new Date(2120, 12, 31),
            onSelect: function() {
                startDate = this.getDate();
                updateStartDate();
            }
        }),
        endPicker = new Pikaday({
            field: document.getElementById('datetimepicker2_".$id."'),
            minDate: new Date(),
            maxDate: new Date(2120, 12, 31),
            onSelect: function() {
                endDate = this.getDate();
                updateEndDate();
            }
        }),
        _startDate = startPicker.getDate(),
        _endDate = endPicker.getDate();

        if (_startDate) {
            startDate = _startDate;
            updateStartDate();
        }

        if (_endDate) {
            endDate = _endDate;
            updateEndDate();
        }
    }
    gdate();
    </script>";


        //         if($inf && $v == "0")
//         {
//             $added2 = '<button onclick="BTNRESET('.$id.');return false;" style="display:none;" class="btn btn-default infinit" id="ini_btn_'.$id.'_'.$inf2.'">Unendlich '.$id.'</button>';
//             $v2 = date("Y-m-d H:i",time());
//             return '<div class="input-group">


//                         <span id="infinityTexts_'.$id.'" style="display:block;" class="infipic"><img src="/images/icons/datetime_inf_dark.jpg" alt="Anfangsdatum Bild"></span>
//                         <input data-id="'.$id.'" data-table="'.$t.'" data-column="'.$n.'"    type="datetime-local" id="datetimes_'.$id.'" name="datetime" value="0" class="datetimepicker2">
//                         <span class="log">
//                     <i class="fa-regular fa-calendar calendar-icon" style="cursor: pointer;" id="licon_'.$id.'_'.$inf2.'"></i>
//                </span>


//                     '.$added2.'
//                     </div>
//                     <script>
//                     inf('.$id.');

//                     </script>';
//             $size = 1;
//             $disp = "none";
//             $added = '';
//         }
//         else
//         {
//             $size = 12;
// $disp = "none";

//             $ov = '';
//             $v2 = date("Y-m-d H:i",$v);
//         }
//         if($inf2)
//         {
//             $disp = 'block';
//              $added = '<button onclick="BTNRESET('.$id.');return false;" style="display:block;" class="btn btn-default infinit" id="ini_btn_'.$id.'_'.$inf2.'">Unendlich '.$id.'</button> <script>
//                     inf(173);

//                     </script>';
//             $span = '<span id="infinityTexts_'.$id.'" style="display:none;" class="infipic"><img src="/images/icons/datetime_inf_dark.jpg" alt="Anfangsdatum Bild"></span>';

//         }

//         return  '<div class="datetime-wrapper  input-group">
//                     '.@$span.'
//                     <input data-id="'.$id.'" data-table="'.$t.'" data-column="'.$n.'" class="datetimepicker2 datetime-input" id="datetimepicker_'.$id.$inf2.'" name="'.$n.'" type="datetime-local" value="'.$v2.'" maxlength="12" size="'.$size.'">

//                     <span class="log">
//                     <i class="fa-regular fa-calendar calendar-icon" style="cursor: pointer;" id="licon_'.$id.'_'.$inf2.'"></i>
//                     </span>
//                  </div>'.@$added;

    }
    public static function date($n,$v,$t,$crea,$id)
    {
        return  '<div id="graphicWrapper" class="datetime-wrapper" style="position: relative; display: inline-block;">
            <input data-id="'.$id.'" data-table="'.$t.'" data-column="'.$n.'" type="date" id="datetimepicker_'.$id.'" class="datetimepicker2" name="'.$n.'" value="'.$v.'" maxlength="12" size="12" style="border-radius:5px;">
        <i class="fa-regular fa-calendar datetime-icon4"></i></div>
    ';
    }
    public function updateDatetime(Request $request)
    {
        // Validierung der Eingabewerte
        // $validated = $request->validate([
        //     'id' => 'required|integer', // ID des Eintrags
        //     // 'value' => 'required|date_format:Y-m-d H:i',
        //     'column' => 'required|string', // Spaltenname
        //     'table' => 'required|string', // Tabelle
        // ]);
            $table = $request->table;
        try {
            //\Log::info([$request->table,$request->column,$request->value]);
            ///\Log::info("ts:",   [strtotime($request->value)]);

            // Aktualisiere den Datensatz in der Datenbank
            $updated = DB::table($table) // Ersetze 'your_name' mit dem Namen deiner Tabelle
                ->where('id', $request->id)
                ->update([$request->column => strtotime($request->value)]);
    $arr = [$request->id,$request->column,strtotime($request->valuef)];
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datetime erfolgreich aktualisiert.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Keine Änderungen vorgenommen.',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Fehler bei der Aktualisierung der Datetime: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der Aktualisierung.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateAge(Request $request)
    {
        // Validierung der Eingabewerte
        // $validated = $request->validate([
        //     'id' => 'required|integer', // ID des Eintrags
        //     // 'value' => 'required|date_format:Y-m-d H:i',
        //     'column' => 'required|string', // Spaltenname
        //     'table' => 'required|string', // Tabelle
        // ]);


            $table = $request->table;
        try {
            //\Log::info([$request->table,$request->column,$request->value]);
            ///\Log::info("ts:",   [strtotime($request->value)]);
            $timestamp = strtotime($request->value);
            $currentValue = DB::table($table)->where('id', $request->id)->value($request->column);
            if ($currentValue === $timestamp) {
                return response()->json(['message' => 'No changes detected'], 200);
            }


            if(empty($request->value) || $timestamp == '-3600')
            {
                return response()->json(['age' => "N/A"]);
            }
            // Aktualisiere den Datensatz in der Datenbank
            $updated = DB::table($table) // Ersetze 'your_name' mit dem Namen deiner Tabelle
                ->where('id', $request->id)
                ->update([$request->column => $timestamp]);

                $age = $timestamp ? Carbon::createFromTimestamp($timestamp)->age : "N/O";
            if ($updated == 0   ) {
                return response()->json(['age' => $age]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Keine Änderungen vorgenommen.',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Fehler bei der Aktualisierung der Datetime: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der Aktualisierung.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public static function date_old($n,$v,$t,$w='',$id)
    {
        changeDatetimeToTimestamp();
        $dateString = $v;
        $id = empty($id) ? "1" : $id;
        // $date = Carbon::createFromFormat('Y-m-d H:i:s', $text);
        // $formattedDate = $date->format('d.m.Y H:i:s');
        // $date = $formattedDate;

        if(!is_int($v))
        {
            $date2 = $v;
        }
        else
        {
            $date2 = $v;
        }




    $scr = CheckRights(Auth::id(),$t,"date") ? $scr : '';
    if($scr)
    {
        $btn =  "<span class='btn btn-default' id='openPicker_".$id."'><i class='fas fa-dateweek'></i></span>";

    }
        return "<div align='left'><nobr><span id='desc_".$id."'>$date2</span>&nbsp;&nbsp;&nbsp;<input type='text' id='datetimepicker_".$id."' value='".$date2."' class='hidden' >".@$btn."</nobr></div>$scr";
    }
    public static function image($n,$v,$t)
    {
        if(empty($v)){
            return;
        }
        return "<img src='".asset("images/$t/$v")."' class='table_thumb'/>";
    }
    public static function up_position($table,$la_id){

        if (Schema::hasColumn($table, "position")) {
        $newpos = 0;

            $newp = 1;
            $gut = DB::table($table)->where('position', $newp)->first(['id']);

            if(!$gut)
            {
                $np[] = $newp;
                $ewpos[] = $newpos;
                // DB::table($table)
                //     ->where('position', $newpos)
                //     ->update(['position' => $newp]);
            }
        }
    }
    public static function getRes(Request $request)
{
    $requestData = $request->all();
    $output = '';

    foreach ($requestData as $key => $value) {
        // Check if the value is an array
        if (is_array($value)) {
            // Convert the array to a JSON string for easier readability
            $output .= "$key: " . json_encode($value) . "\n";
        } else {
            // Concatenate key-value pairs into a string
            $output .= "$key: $value\n";
        }
    }

    // vdd($output); // Dump the concatenated string for debugging
}
    public static function updateOrderFC(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'sorted_ids' => 'required|array',
        ]);

        $table = $request->input('table');
        $order = $request->input('sorted_ids');
        $fieldsToUpdate = ['position'];
        // vdd($order);
        foreach ($order as $position => $id) {
        $data['position'] = $position;
        DB::statement('UPDATE '.$table.' SET position = :position WHERE id = :id', ['id' => $id,"position"=>$position]);

        //DB::table($table)->where('id', $id)->update($data);

        }




        return response()->json(['status' => 'success']);
    }

    public static function updatePositionsFC(Request $request)
    {
        $sortedIds = $request->input('sorted_ids');
        $newPositions = $request->input('new_positions'); // Get new positions from the request

        $oldPosition = [];
        // Loop through the sorted IDs and store the old position
        foreach ($sortedIds as $id) {
            $oldPosition[$id] = DB::table($request->table)->where('id', $id)->value('position');




            // Check if the new position exists for this ID and is not null
            if (isset($newPositions[$id]) && !is_null($newPositions[$id])) {
                // Update the position in the database
                DB::table($request->table)->where('id', $id)->update(['position' => $newPositions[$id]]);
            }
        }

        // Get user rights from 'users_rights' table
        $userRights = DB::table('users_rights')->orderBY('id')->get();

        // Process bit values for each right
        foreach ($userRights as $rights) {
            $rid = empty($rights->id) ? ($rights->id+1):$rights->id;

            // Convert the bit strings into arrays
            $viewTable = str_split($rights->view_table);
            $editTable = str_split($rights->edit_table);
            $publishTable = str_split($rights->publish_table);
            $addTable = str_split($rights->add_table);
            $deleteTable = str_split($rights->delete_table);
            $dateTable = str_split($rights->date_table);


            // Log rights ID for debugging
            $user = Auth::user();
            if($user->users_rights_id == $rid)
            {
                FormController::moveBitValue($viewTable, $oldPosition[$id], $newPositions[$id],$rid,'view');
                FormController::moveBitValue($editTable, $oldPosition[$id], $newPositions[$id],$rid,'edit');
                FormController::moveBitValue($publishTable, $oldPosition[$id], $newPositions[$id],$rid,'publish');
                FormController::moveBitValue($addTable, $oldPosition[$id], $newPositions[$id],$rid,"add");
                FormController::moveBitValue($deleteTable, $oldPosition[$id],$newPositions[$id],$rid,'delete');
                FormController::moveBitValue($dateTable, $oldPosition[$id], $newPositions[$id],$rid,"date");
            }

            // Move bit values in the arrays
            foreach ($sortedIds as $id) {
                if (isset($newPositions[$id])) {
                    FormController::moveBitValue($viewTable, $oldPosition[$id], $newPositions[$id],$rid,'view');
                    FormController::moveBitValue($editTable, $oldPosition[$id], $newPositions[$id],$rid,'edit');
                    FormController::moveBitValue($publishTable, $oldPosition[$id], $newPositions[$id],$rid,'publish');
                    FormController::moveBitValue($addTable, $oldPosition[$id], $newPositions[$id],$rid,"add");
                    FormController::moveBitValue($deleteTable, $oldPosition[$id],$newPositions[$id],$rid,'delete');
                    FormController::moveBitValue($dateTable, $oldPosition[$id], $newPositions[$id],$rid,"date");
                }
                else
                {



                }

            }

            // Update the modified rights back into the database
            DB::enableQueryLog();

            if($user->users_rights_id == "1" && $rid == $user->users_rights_id)
            {
                DB::table('users_rights')
                ->where('id', $user->users_rights_id)
                ->update([
                    'view_table' => implode('', $viewTable),
                    'edit_table' => implode('', $editTable),
                    'publish_table' => implode('', $publishTable),
                    'add_table' => implode('', $addTable),
                    'delete_table' => implode('', $deleteTable),
                    'date_table' => implode('', $dateTable),
                ]);


            }
            DB::table('users_rights')
                ->where('id', $rid)
                ->update([
                    'view_table' => implode('', $viewTable),
                    'edit_table' => implode('', $editTable),
                    'publish_table' => implode('', $publishTable),
                    'add_table' => implode('', $addTable),
                    'delete_table' => implode('', $deleteTable),
                    'date_table' => implode('', $dateTable),
                ]);


        }

        return response()->json(['message' => 'Positions updated successfully.']);
    }
    /**
     * Moves a bit value within an array from the old position to the new position.
     */
    public static function moveBitValue(&$array, $oldPosition, $newPosition,$rid,$type)
    {
        $ids[$type] = $ids[$type] ?? [];
        // foreach($oldPositions as $id=>$oldPosition)
        // {
            $oldPosition = ($oldPosition);
            $newPosition = $newPosition;
            if( $oldPosition > $newPosition)
            {


            // Prüfen, ob der alte Position existiert
            if (isset($array[$oldPosition])) {
                // Hole den Bitwert an der alten Position
                $bitValue = $array[$oldPosition];

                // Entferne den Bitwert von der alten Position
                unset($array[$oldPosition]);

                // Füge den Bitwert an der neuen Position ein
                // array_splice fügt den Wert vor dem angegebenen Index ein
                if(!in_array($type,$ids[$type]))
                    {

                        array_splice($array, $newPosition, 0, $bitValue);

                        $ids[$type][] = $type;
                    }


            }
            // }




    }
}

    // Dynamische Methode für das Geburtsdatumsfeld
    public static function BdayField($name, $value = '')
    {
        $value = $value;
//         return "
// <div class='input-group datepicker-wrapper'>
//     <input type='date' name='birthday' value='$value' class='form-control'>
//     <span class='input-group-text datepicker-icon'>
//         <i class='fas fa-calendar'></i>
//     </span>
// </div>";

        return "<input type='date' name='{$name}' value='{$value}' class='form-control'>";
    }

    // Dynamische Methode für das Bild-Uploadfeld
    public static function Upload($name, $value = NULL,$table,$create)
    {
        $xx = "<input type='file' name='{$name}' id='{$name}' class='form-control'>";
        if ($value && !$create)
        {
              $xx .= 'Aktuelles Bild: <img src="'.asset('images/'.$table.'/'.$value.'').'" alt="Aktuelles Bild" width="100"></p>';
        }
        return $xx;


        // <div class='progress mt-2'>
        //     <div id='upload-progress' class='progress-bar' role='progressbar' style='width: 0%;' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100'>0%</div>
        // </div>";
    }
    public static function UploadClick($name,$value,$table,$id,$create='update',$ulf)
    {

        if(empty($id))
        {
            $id = "12";
        }
        if(empty($value) && $create != 'index')
        {
            $value = 'spacer.jpg';
        }
        elseif($create == "index" && empty($value))
        {
            $value = "spacer_new.gif";
        }
        if(is_dir(public_path("images/$table/thumbs")) || is_dir(public_path("images/_".SD()."/$table/thumbs")))
        {
            $thumb = "thumbs/";
        }
        $create = Str::afterLast(url()->current(), '/');

        if(is_dir(public_path("images/_".SD()."/".$table."/".@$thumb)))
        {
           // $dom = "_".SD()."/";
        }
        elseif((!file_exists(public_path("images/".$table."/".$value."")) && !empty($value)) || $create == "create"){
            $value = "spacer.jpg";
            $thumb = '';
        }
    return '
    <script>
    var $j = jQuery.noConflict(); // Weist jQuery einer anderen Variable zu, um Konflikte zu vermeiden";
    </script>
    <button class="myBtn" id="myBtn_'.$id.'" type="button" OnCLick="document.getElementById(\'myModal_'.$id.'\').style.display=\'block\';">

</button>'."\n\n".FormController::modbox($name,$value,$table,$id,'');
 }

    public static function modbox($name,$value,$table,$id,$ulf='')
    {
        $xxu = !$ulf ? '<form method="post" id="ul_form_'.$id.'" enctype="multipart/form-data" action="/imul/uploadcreate">' : '';
        $xfu = !$ulf ? "</form>" : "";
        $xtu = !$ulf ? 'ul_form_'.$id : "uploadForm";
        $xx =   $xxu.
                "<input type='hidden' name='_token' value='" . csrf_token() . "'>".
                "<input type='hidden' name='id' value='" . $id . "'>".
              '<div class="modal fade in" id="myModal_'.$id.'" role="dialog" tabindex="-1">
              <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title">Bild Hochladen</h4>

                      <button type="button" class="close" id="closeup_'.$id.'" OnCLick="document.getElementById(\'myModal_'.$id.'\').style.display=\'none\';">&times;</button>

                      </div>
                      <div class="modal-body">
                      ';
                      $xx .= '<div class="copy_sel">'.FormController::CopyField($name,$value,$table,$id).'</div>';
                      $xx .=  '<input type="hidden" name="_token" value="' . csrf_token() . '">';
                      $xx .= '<input type="hidden" name="id" value="'.$id.'">';
                      $xx .= '<input type="hidden" name="table" value="'.$table.'">
                      <input type="file" name="image_path" id="fileInput_'.$id.'" class="hidden">

                          <div class="upload-area" id="ul_label_'.$id.'"><label class="cursor">Hier herziehen oder klicken zum Upload</label>
                          <div class="upload-preview" id="preview_'.$id.'"></div>
                          </div>
                          <div id="progress-container_'.$id.'" class="progress" style="display:none; margin-top: 10px;">
                            <div id="progress-bar_'.$id.'" class="progress-bar" role="progressbar" style="width:0%">
                            0%
                            </div>
                          </div>

                      </div>
                      <div class="model_subm"></div>
                      <div class="modal-footer">
                       <button class="btn btn-success" OnClick="$j(\'#'.$xtu.'\').submit();">Speichern</button>
                          <button type="button" class="btn btn-default" id="closedown_'.$id.'" OnCLick="document.getElementById(\'myModal_'.$id.'\').style.display=\'none\';">Schliessen</button>
                      </div>
                  </div>

              </div>
          </div>
          </div>
          '.$xfu.'

          <script>

          ';




$xx .= '
document.addEventListener("DOMContentLoaded", function() {

var $j = jQuery.noConflict(); // Weist jQuery einer anderen Variable zu, um Konflikte zu vermeiden
      $j("#myBtn_'.$id.'").on("click", function() {
         $j("#uploadModal_'.$id.'").modal("show");  // Modal öffnen
    });
      $j("#closedown_'.$id.'").on("click", function() {
         $j("#uploadModal_'.$id.'").modal("hide");  // Modal schliessen
    });
      $j("#closeup_'.$id.'").on("click", function() {
         $j("#uploadModal_'.$id.'").modal("hide");  // Modal schliessen
    });
});

var $k = jQuery.noConflict();
    </script>';
      $xxa = "<script>



        let uploadArea = \$k('#ul_label_".$id."');
        let fileInput = \$k('#fileInput_".$id."');
        let previewArea = \$k('#preview_".$id."'); // Vorschau-Bereich
        let progressBar = \$k('#progress-bar_".$id."'); // Fortschrittsbalken
        let progressContainer = \$k('#progress-container_".$id."'); // Fortschrittsbalken-Container
        let uploadForm = \$k('#uploadForm');
        // Klick auf den Upload-Bereich öffnet den Datei-Dialog
        uploadArea.on('click', function() {
            fileInput.click();
        });

        // Wenn Dateien per Drag & Drop über den Upload-Bereich gezogen werden
        uploadArea.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.addClass('dragging');
        });

        // Entfernt den 'dragging'-Zustand, wenn der Bereich verlassen wird
        uploadArea.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.removeClass('dragging');
        });
        uploadForm.on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        // Wenn Dateien fallen gelassen werden
        uploadArea.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.removeClass('dragging');

            // Hole die Dateien
            let files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                // Füge die Datei in das versteckte Input-Element ein
                fileInput[0].files = files;

                // Zeige Vorschau der Datei
                showImagePreview(files[0]);

                // Starte den Upload
                uploadFile(files[0]);
            }
        });

        // Dateiauswahl über das Input-Feld
        fileInput.on('change', function(e) {
            if (e.target.files.length > 0) {
                // Zeige Vorschau der ausgewählten Datei
                showImagePreview(e.target.files[0]);

                // Starte den Upload
                uploadFile(e.target.files[0]);
                const xFile = e.target.files[0];
            }
        });

        // Funktion zur Anzeige der Bildvorschau
        function showImagePreview(file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                previewArea.empty(); // Leere den Vorschau-Bereich
                let img = \$k('<img>').attr('src', e.target.result).css({ width: '100px', height: 'auto' });
                previewArea.append(img); // Füge das Bild zur Vorschau hinzu
            };
            reader.readAsDataURL(file);
        }

        // Funktion zum Hochladen der Datei
        function uploadFile(file) {
            let formData = new FormData();
            formData.append('file', file);
            formData.append('image_path', file);
            formData.append('id', '');
            formData.append('_token', \$k('meta[name=\"csrf-token\"]').attr('content')); // CSRF-Token hinzufügen

            // Fortschrittsbalken sichtbar machen
            progressContainer.show();
            \$k('#pb').show();
            jQuery.ajax({
                url: '/imul/upload', // Passe den Upload-URL an
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    let xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            let percentComplete = (e.loaded / e.total) * 100;
                            progressBar.css('width', percentComplete + '%');
                            progressBar.text(Math.round(percentComplete) + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {

                    progressBar.css('width', '100%');
                    progressBar.text('100%');
                },
                error: function() {
                    alert('Fehler beim Hochladen der Datei.');
                    progressContainer.hide();
                },
                complete: function() {
                    // Fortschrittsbalken nach dem Upload verstecken
                    setTimeout(function() {
                        progressContainer.hide();
                        progressBar.css('width', '0%');
                        progressBar.text('0%');
                    }, 2000);
                }
            });
        }";
$xx .= "<script>
\$k(document).ready(function() {
upload_files($id);
    });
</script>
<script></script>";
      return $xx;
    }
    public static function CopyField($name,$value,$table,$id)
    {
        if(in_array($table,Settings::$copyright_tables))
        {
            $options = '';
            $opt = DB::table("camera")
                ->select("id", "short_tag", "exif_copyright")
                ->where("pub", "1")               // Filtert nur Einträge, bei denen pub den Wert "1" hat
                // ->groupBy("camera.short_tag")             // Gruppiert die Einträge nach short_tag
                ->orderBy("short_tag", "ASC")      // Sortiert die Ergebnisse aufsteigend nach short_tag
                ->get();
                $sot = array();
            foreach($opt as $opti)
            {
                if(!in_array($opti->short_tag,$sot))
                {
                    $options .= "<option value='".$opti->short_tag."'>".$opti->exif_copyright."</option>";
                    $sot[] = $opti->short_tag;
                }


            }
            return "<b>Copyrightbanner:</b>
                    <select name='copyrightbanner'>
                    $options

                    </select>";
        }

    }
 public static function uid($name,$val,$value,$field,$table,$fiel,$id)
    {
            $cat = DB::table($table)
        ->where('id', $val) // Replace $id with the actual user ID
        ->value($fiel); // Get the value of the 'name' column

    //     return $nick;
        return "<span data-id='$id' data-column='$name' class='sele'>".ucfirst($cat)."</span>".FormController::ImSelect($name,$val,"hidden2",$table,$fiel,$id);
    }
    public static function ImSelect($name, $selectedValue = '', $hidden = '',$table,$fiel,$id,$coled='')
    {
        // Setze die Subdomain-basierte Datenbankverbindung
        $subdomain = SD();
       // \Log::info("vals:".json_encode(compact('name','selectedValue','hidden','table','fiel','id')));

      // Führe die Abfrage auf der ausgewählten Verbindung aus
        $authors = DB::table($table)
            ->where('pub', '1')
            ->orderBy($fiel, 'ASC')
            ->get();

        $options = '';

        foreach ($authors as $author) {
            if (!empty($selectedValue))
            {
                $selected = $selectedValue == $author->id ? 'selected' : '';
            }
            else
            {
                $selected = '';
            }

            $options .= "<option value='{$author->id}' {$selected}>{$author->name}</option>";
        }
        $alt  = $coled ? "_alt" : '';

        return "<input type='hidden' id='f_".$id."' value='{$name}'><select class='form-control a_sel$alt {$hidden}' data-id='{$id}' data-column='{$name}' name='{$name}'>{$options}</select>";
    }
    // Dynamische Methode für die Auswahl des Autors


    // Methode zur Verarbeitung des Formulars
    public static function submitForm(Request $request)
    {
        $validatedData = $request->validate([
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'author_id' => 'required|exists:authors,id',
            'birthday' => 'required|date',
        ]);

        // Bild hochladen
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('uploads', 'public');
        }

        // Erfolgsnachricht anzeigen
        return back()->with('success', 'Formular erfolgreich abgesendet!');
    }
    public static function textArea($name,$value,$id)
    {
        $req = Settings::$no_req;
        $reqi = 'required';
        if (in_array($name, $req))
        {
            $reqi = '';
        }
        \Log::info("REQUI: ".$reqi);
        if(Settings::$textfield == "Mdown")
        {
            return TextArea_md($name,$reqi,$value);
        }
        elseif(Settings::$textfield == "HTML")
        {
            return TextArea_htm($name,$reqi,$value);
        }
        $xx = "<input type='hidden' id='text_hidden_".$name."_".$id."' value='$value'>";

        return "$xx<br /><textarea rows='4' cols='65' name='$name' id='$name' {$reqi}>".smilies($value)."</textarea>";
    }
    public static function Tfield($name,$value)
    {
        $req = Settings::$no_req;
        $reqi = 'required';
        if (in_array($name, $req))
        {
            $reqi = '';
        }

        $value = $name == "queue" ? "123" : $value;
        return "<input type='text' $reqi name='$name' class='form-control' value='".smilies($value)."' id='$name'>";
    }
    public static function idField($name,$value)
    {
        return "<input type='text' name='$name' class='form-control' value='$value' id='$name' disabled>";
    }
    public static function pass($name,$value)
    {
        return "<input type='password' class='form-control' name='$name' value='$value' id='$name'>";
    }
    public static function changeDate(Request $request)
    {
        // Validierung des Datums
        $validatedData = $request->validate([
            'date' => 'required|date_format:"d.m.Y"',
            'table' => 'required|string',
            'id'    => 'required|int',
            'column'    => 'required|string'
        ]);

        // Verarbeite das Datum, z.B. speichere es in der Datenbank
        // $date = $validatedData['date'];
        $data = $validatedData;
        $request->date = strtotime($request->date);


        // Beispiel: Datum speichern
        // YourModel::create(['date' => $date]);
        DB::table($request->table)->where('id', $request->id)->update([$request->column => $request->date]);




        return response()->json(['success' => 'Datum wurde erfolgreich gespeichert.']);
    }

}

