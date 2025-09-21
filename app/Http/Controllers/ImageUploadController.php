<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;





use Storage;

class ImageUploadController extends Controller
{
    public function upload_ori(Request $request,$table,$iswatermark="1")
    {
        return $this->upload($request,$table,$iswatermark,1);
    }
    public function upload(Request $request,$table,$iswatermark='1',$oripath='0')
    {
        // Validierung der Eingabedaten
        // $validated = $request->validate([
        //     'image' => 'image|mimes:jpeg,png,jpg,gif',
        //     'table' => 'string',
        //     'column' => 'string',
        //     'path' => 'string',
        //     'name' => "string"
        // ]);

            // \Log::info($request->all());
        // Das hochgeladene Bild holen

        $image = $request->file('image');
        $is_imgdir = $request->is_imgdir;
            if($is_imgdir){


        $is_imgdir = str_replace("//","/",$is_imgdir);
        \Log::info("iid: ".$is_imgdir);
        $is_imgdir = explode("/",$is_imgdir);
        $is_imgdir = $is_imgdir[count($is_imgdir)-2];

    }
        $subdomain = SD();
        \Log::info(CleanTable().$subdomain.$request->column);
        $Message = $request->Message === "true" ? true : false;
        if (!$request->hasFile('image')) {
           // \Log::info('Keine Datei empfangen!');
            return response()->json(['error' => 'Keine Datei empfangen!'], 400);
        }
        else{
             //\Log::info("Table: ".json_encode($request->table));
        }
        //\Log::info('Datei-Info:', [
       //     'original_name' => $image->getClientOriginalName(),
         //   'mime_type' => $image->getMimeType(),
           // 'size' => $image->getSize()
       // ]);
        $image = $request->file('image') ?? [];
        $path = $request->ulpath;
//$table_dir = Settings::$image_paths[$request->table];
        $watermarkfile = $request->copyleft;
        // \Log::info("WMF: ".$watermarkfile);
        $table = $table_ori = $request->table;
        $column = $request->column;

            // \Log::info(($Message));

        // Den Dateinamen generieren und Bild speichern
        $imageName = md5($image->getClientOriginalName()."_".Auth::id()).".".$image->getClientOriginalExtension();
        //$imagePath = $image->storeAs($path, $imageName, 'public');

        $filename = basename($imageName);
        // Speicherpfad definieren
        $sizes = [1200];
        $tmpname = $_FILES['image']['tmp_name'];
       if(!$Message && !array_key_exists($table, Settings::$impath)){
        $IMOpath =  public_path("images/_{$subdomain}/{$table}/{$column}/{$is_imgdir}/orig/".basename($filename)."");

        copy($tmpname,$IMOpath);

        $sizes = [350, 800, 1400];
        }
        elseif($Message)
        {
            $table = "messages";
            // $prepath = "/images/messages/";
        }
        if(array_key_exists($table_ori, Settings::$impath)){
            $sizes = ["500"];
            $table = '';
        }
        list($oldsize,$oldheight) = getimagesize($tmpname);

        // Bildgrößen anpassen und speichern
          // Beispiel für 3 verschiedene Auflösungen
        $big = ["350"=>"/thumbs/","1200"=>'/','500'=>"/","800"=>'/',"1400"=>"/big/"];
        foreach ($sizes as $size) {

            if($size > $oldsize)
            {
                $size2 = $oldsize;
            }
            else{

                $size2 = $size;
            }
            $resizedPath = public_path("/images/_{$subdomain}/{$request->table}/{$column}/{$is_imgdir}{$big[$size]}".basename($imageName))."";
            if(!$oripath)
            {
                $subdomain = SD();
                $resizedPath = public_path("/images/_{$subdomain}/{$table}{$big[$size]}".basename($imageName))."  ";
            }

            // \Log::info("RP:".$resizedPath);
            // Neues Imagick-Objekt erstellen
            $imagick = new \Imagick();
            $imagick->readImage($image->getPathname());

            // Bildgröße anpassen
            $imagick->resizeImage($size2, 0, \Imagick::FILTER_LANCZOS, 1); // 0 für automatische Höhe

            // Speicherpfad für jede Version
            if($iswatermark && $big[$size] != "/thumbs/" && !empty($watermarkfile) && !$Message)
            {
                $imagePath = $tmpname;
                $watermarkPath = public_path("images/copyleft/".$watermarkfile.".png");

                // Neues ImageMagick-Objekt erstellen
                $imagick = new \Imagick($imagePath);
                $imagick->resizeImage($size2, 0, \Imagick::FILTER_LANCZOS, 1); // 0 für automatische Höhe
                // Wasserzeichenbild laden
                $watermark = new \Imagick($watermarkPath);

                // Berechne die Position des Wasserzeichens: rechte untere Ecke
                $xPosition = $imagick->getImageWidth() - $watermark->getImageWidth() - 10; // 10px Abstand vom Rand
                $yPosition = $imagick->getImageHeight() - $watermark->getImageHeight() - 10; // 10px Abstand vom Rand

                // Setze das Wasserzeichen auf das Bild
                $imagick->compositeImage($watermark, \Imagick::COMPOSITE_OVER, $xPosition, $yPosition);

                // Speichern des neuen Bildes mit Wasserzeichen
                $imagick->writeImage($resizedPath);
            }
            else{
                $imagick->writeImage($resizedPath);
            }
            // Bild speichern

            if($size == 1400 || $Message || $size == "500")
            {
                list($width,$height) = getimagesize($resizedPath);

                $imageName = "/".$resizedPath;
            }

            // Speicher freigeben
            $imagick->clear();
            $imagick->destroy();
            $url = "/".str_replace("/big/","/",$resizedPath);
            $name = $request->name;


        }

        if($is_imgdir){
            $this->AddJson($path."/".$is_imgdir,$filename);
        }
        if($Message){
            $imageName = ($imageName);
            // \Log::info($Message);
        }

        $imageName = str_replace("/images/_".$subdomain."/".$table."/".$column."//big/",'',basename($imageName));
        $imageName = $column."/".basename($imageName);
        \Log::info($imageName);
        // $iid = DB::table($table_dir)->insertGetId([
        //     'name' => $name,
        //     'url' => $url,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
        //\Log::info(json_encode(['message' => $imageName]));
        return response()->json([
            'message' => 'Bild erfolgreich hochgeladen.',
            'image_url' => $imageName,
            "img_x" => $width,
            "img_y" => $height,
        ]);
    }
    public function save(Request $request,$table)
    {
        // \Log::info("resa:".$table);

    }
    public function CopyLeft()
    {
        $cl = DB::table('copyleft')->select('tag', 'name')->get();
        return response()->json([
            'message' => 'Copyleft Success',
            'copyleft' => $cl,
        ]);

    }
    public function store_json(Request $request)
    {
        $data = $request->images;
        // dd($request->all());
        // Pfad zur Datei z. B. via folder:
        $folder = $request->input('folder');
        $path = public_path($folder . '/index.json');
        \Log::info($data);

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return response()->json(['success' => true]);
    }
    public function AddJson($folder,$fn)
    {
        if(!is_file(public_path("/images/".$folder."/index.json")))
        {
            file_put_contents(public_path("/images/".$folder."/index.json"), json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        $oldjs = file_get_contents(public_path("/images/".$folder."/index.json"));

        $array = json_decode($oldjs);


        // Neue Werte
        $filename = $fn;
        $size = getimagesize("images/".$folder."/big/".$fn);
        $w = $size[0];
        $h = $size[1];

        // Letzte Position ermitteln
        $lastPosition = 0;
        if (!empty($array)) {
            $last = end($array);
            $lastPosition = $last->position;
        }
        $filenames = array_map(fn($item) => $item->filename, $array);

        if (!in_array($fn, $filenames))
        {
            // Neues Objekt anhängen
            $array[] = (object)[
                'position' => $lastPosition + 1,
                'filename' => $filename,
                'label' => '',
                'width' => $w,
                'height' => $h,
        ];
        }
        file_put_contents(public_path("/images/".$folder."/index.json"), json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    function Del_Image($column,$folder,$posi=1){
        if(!CheckRights(Auth::id(),CleanTable(),"delete"))
        {
            return response()->json(["success"=>"false","message"=>"Rechte nicht vorhanden"]);
        }
        $path = public_path("/images/_".SD()."/images/".$column."/".$folder."/");
        $oldjs = file_get_contents(public_path("/images/_".SD()."/images/".$column."/".$folder."/index.json"));
        $oldjs = json_decode($oldjs,true);
        $newjs = $this->deleteImageByPosition($oldjs,($posi+1),$path);
        \Log::info("old: ".json_encode($oldjs,JSON_PRETTY_PRINT));
        \Log::info("new: ".json_encode($newjs,JSON_PRETTY_PRINT));
        file_put_contents(public_path("/images/_".SD()."/images/".$column."/".$folder."/index.json"), json_encode($newjs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return response()->json(["success"=>"true","message"=>"Bild wurde erfolgreich gelöscht"]);
    }
    function deleteImageByPosition(array $images, int $deletePosition, string $path): array
{
    // Wenn nur 1 Element vorhanden ist und das gelöscht werden soll → leere JSON
    if (count($images) === 1 && $images[0]['position'] == $deletePosition) {
        \Log::info("P:".$path);
        $file = $images[0]['filename'];
        if (is_file($path.$file)) {
            @unlink($path."thumbs/".$images[0]['filename']);
            @unlink($path."orig/".$images[0]['filename']);
            @unlink($path."big/".$images[0]['filename']);
            @unlink($path."".$file);
            file_put_contents($path."index.json","[]");

        }
        return []; // JSON wird geleert
    }

    // Normales Entfernen des Elements mit der gewünschten Position + Datei löschen
    $images = array_filter($images, function($item) use ($deletePosition, $path) {
        if ($item['position'] === $deletePosition) {
            $file = $item['filename'];
            if( is_file($path."/".$file)) {
                @unlink($path."/thumbs/".$item['filename']);
                @unlink($path."/orig/".$item['filename']);
                @unlink($path."/big /".$item['filename']);
                @unlink($path."/".$file);
            }
            return false; // Entfernen
        }
        return true;
    });

    // Neu indizieren
    $images = array_values($images);

    // Positionen neu zuweisen
    foreach ($images as $index => &$item) {
        $item['position'] = $index + 1;
    }

    return $images;
}

    function old_deleteImageByPosition(array $images, int $deletePosition, string $path): array
{
    // 1. Entferne das Element mit der gewünschten Position und lösche die Datei
    $images = array_filter($images, function($item) use ($deletePosition, $path) {
        if ($item['position'] === $deletePosition) {
            $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item['filename'];
            if (is_file($file)) {
                unlink($path."/thumbs/".$item['filename']);
                unlink($path."/orig/".$item['filename']);
                unlink($path."/big /".$item['filename']);
                unlink($file);
            }
            return false; // Element wird entfernt
        }
        return true; // Element bleibt
    });

    // 2. Neu indizieren
    $images = array_values($images);

    // 3. Positionen neu zuweisen
    foreach ($images as $index => &$item) {
        $item['position'] = $index + 1;
    }

    return $images;
}
    public function update(Request $request)
    {
        $user = $request->user();

        // Validierung (erweitere Regeln nach Bedarf)
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'photo' => 'nullable|image|max:4096', // bis 4MB
        ]);

        // === Dateiverarbeitung ===
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            if (!$file->isValid()) {
                return back()->withErrors(['photo' => 'Upload fehlgeschlagen.']);
            }

            // Subdomain (erstes Segment des Hostnames)
            $host = $request->getHost();
            $subdomain = explode('.', str_replace("www.","",$host))[0] ?? 'default';

            // Zielverzeichnis im public-Ordner
            $folder = public_path("images/_{$subdomain}/users/profile_photo_path");

            // Ordner anlegen, falls nicht vorhanden
            if (!File::exists($folder)) {
                // 0755; setze recursive true
                File::makeDirectory($folder, 0777, true);
            }

            // Alten File löschen (falls vorhanden)
            if (!empty($user->profile_photo_path)) {
                $oldPath = $folder . DIRECTORY_SEPARATOR . $user->profile_photo_path;
                if (File::exists($oldPath)) {
                    try {
                        File::delete($oldPath);
                    } catch (\Exception $e) {
                        // optional loggen, aber nicht blockieren
                        \Log::warning('Altes Profilbild konnte nicht gelöscht werden: '.$e->getMessage());
                    }
                }
            }

            // Neuer Dateiname (Zeitstempel + uniqid)
            $filename = md5($file->getClientOriginalName()."_".Auth::id()).".".$file->getClientOriginalExtension();

            // Datei verschieben
            $file->move($folder, $filename);

            // In DB nur den Dateinamen speichern (so wie Vue computedPhotoUrl erwartet)
            $user->profile_photo_path = $filename;
        }

        // === Restliche Felder speichern ===
        $user->name = $request->input('name');
        if ($request->has('first_name')) {
            $user->first_name = $request->input('first_name');
        }
        $user->email = $request->input('email');

        $user->save();

        // Wenn du Inertia/Jetstream verwendest: Inertia-Redirect / response
        // Wir redirecten zurück — Inertia-Frontend macht onSuccess + reload auth
        return back(303)->with('success', 'Profil aktualisiert');
    }


    public function getProfilePhoto(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'path' => $user->profile_photo_path
                ? asset($user->profile_photo_path)
                : asset('images/default_profile.png'),
        ]);
    }

}
