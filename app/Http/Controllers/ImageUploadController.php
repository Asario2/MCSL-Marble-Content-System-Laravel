<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ImageUploadController extends Controller
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());


    }

    public function upload_ori(Request $request, string $table, string $iswatermark = "1"): JsonResponse
    {
        return $this->upload($request, $table, $iswatermark, 1);
    }

    public function upload(Request $request, string $table, string $iswatermark = '1', string|int $oripath = '0'): JsonResponse
{
    \Log::debug('UPLOAD DEBUG', [
        'table'   => $request->table,
        'column'  => $request->column,
        'is_imgdir' => $request->is_imgdir,
        'ulpath'  => $request->ulpath,
        'hasFile' => $request->hasFile('image'),
        'Message' => $request->Message,
    ]);
//     \Log::info("IMA: ".$request->ulpath);
    if (!$request->hasFile('image')) {
        return response()->json(['error' => 'Keine Datei empfangen!'], 400);
    }

    $image = $request->file('image');
    $is_imgdir = $request->is_imgdir;
    if ($is_imgdir === "undefined" || empty($is_imgdir)) {
        $is_imgdir = null;
    } else {
        $is_imgdir = str_replace("//", "/", $is_imgdir);
        $parts = explode("/", $is_imgdir);
        $is_imgdir = $parts[count($parts) - 2] ?? null;
    }

    $subdomain = SD();
    $Message = $request->Message == "1";
//     \Log::info("POST",$request->all());
    $path = $request->ulpath;
    if ($path === "undefined" || empty($path)) $path = '';
    $watermarkfile = $request->copyleft;
    $column = $request->column;

    // WICHTIG: MD5 Name VOR table-Zuweisung generieren
    $imageName = md5($image->getClientOriginalName() . "_" . Auth::id()) . "." . $image->getClientOriginalExtension();
    $filename = basename($imageName);

    $sizes = [1200]; // default
    $tmpname = $image->getRealPath();

    // Table-Logik korrigiert
    $table_ori = $request->table;
    $table = $table_ori; // Standardwert

    if ($Message) {
        $table = "messages";
        $sizes = [1200];
    } elseif (!array_key_exists($table_ori, Settings::$impath)) {
        // Nur orig Ordner erstellen wenn nicht Message und nicht special table
        $IMOpath = public_path("images/_{$subdomain}/{$table}/{$column}/{$is_imgdir}/orig/" . $filename);
        if (!File::exists(dirname($IMOpath))) {
            File::makeDirectory(dirname($IMOpath), 0777, true, true);
        }
        copy($tmpname, $IMOpath);
        $sizes = [350, 800, 1400]; // thumbs, normal, big
    }

    if (array_key_exists($table_ori, Settings::$impath)) {
        $sizes = [500];
        $table = '';
    }

    $imgSize = @getimagesize($tmpname);
    if (!$imgSize) return response()->json(['error' => 'Bild konnte nicht gelesen werden!'], 400);
    [$oldsize, $oldheight] = $imgSize;

    // Pfad-Definitionen korrigiert
    $big = [
        "350" => "thumbs/",
        "1200" => '/',
        "500" => "/",
        "800" => '/',
        "1400" => "big/"
    ];

    $width = null;
    $height = null;

    foreach ($sizes as $size) {
        $size2 = ($size > $oldsize) ? $oldsize : $size;

        // KORRIGIERTE Pfad-Logik
        if ($Message) {
            // Für Messages: einfacher Pfad
            $resizedPath = public_path("/images/_{$subdomain}/messages/{$filename}");
        } elseif ($oripath && $is_imgdir) {
            // Mit oripath und imgdir
            $resizedPath = public_path("/images/_{$subdomain}/{$table_ori}/{$column}/{$is_imgdir}/{$big[$size]}{$filename}");
        } elseif ($is_imgdir) {
            // Nur imgdir
            $resizedPath = public_path("/images/_{$subdomain}/{$table_ori}/{$column}/{$is_imgdir}/{$big[$size]}{$filename}");
        } else {
            // Standard
            $resizedPath = public_path("/images/_{$subdomain}/{$table_ori}/{$column}/{$big[$size]}{$filename}");
        }
        $rp = basename($is_imgdir);

        \Log::debug("Creating image for size {$size}", [
            'path' => $rp,
            'Message' => $Message,
            'table_ori' => $table_ori,
            'table' => $table,
            'is_imgdir' => $is_imgdir
        ]);

        if (!File::exists(dirname($resizedPath))) {
            File::makeDirectory(dirname($resizedPath), 0777, true, true);
        }

        try {
            $img = $this->imageManager->read($tmpname);

            // Thumbnail quadratisch
            if ($size == 350) {
                $img->cover($size2, $size2);
            } else {
                $img->scale(width: $size2);
            }

            // Wasserzeichen (nur wenn nicht Message und nicht Thumb)
            if ($iswatermark == '1' && $size != 350 && !empty($watermarkfile) && !$Message) {
                $watermarkPath = public_path("images/copyleft/" . $watermarkfile . ".png");
                if (file_exists($watermarkPath)) {
                    $watermark = $this->imageManager->read($watermarkPath);
                    $watermark->scale(height: 50);
                    $img->place($watermark, 'bottom-right', 10, 10);
                }
            }

            $img->save($resizedPath, quality: 90);

            \Log::debug("Image saved successfully", ['size' => $size, 'path' => $resizedPath]);

            // Größen für Response speichern
            if ($size == 1400 || $Message || $size == 500) {
                $imgSize2 = @getimagesize($resizedPath);
                if ($imgSize2) {
                    [$width, $height] = $imgSize2;
                }
            }

        } catch (\Exception $e) {
            \Log::error("Image processing error for size {$size}: " . $e->getMessage());
            return response()->json(['error' => 'Bildverarbeitung fehlgeschlagen: ' . $e->getMessage()], 500);
        }
    }

    // JSON für imgdir hinzufügen (nur wenn nicht Message)
    if ($is_imgdir && !$Message) {
        $this->AddJson($path . "/" . $is_imgdir, $filename);
    }

    // KORRIGIERTE Response Pfad-Erstellung
    if ($Message) {
        $fullPath = "/images/_{$subdomain}/messages/{$filename}";
    } elseif ($is_imgdir) {
        $fullPath = basename($rp);
    } else {
        $fullPath = $filename; //"/images/_{$subdomain}/{$table_ori}/{$column}/{$filename}";
    }

    \Log::info('Upload completed successfully', [
        'filename' => $filename,
        'fullPath' => $fullPath,
        'Message' => $Message,
        'final_filename' => $filename // Sollte MD5 sein
    ]);

    return response()->json([
        'message' => 'Bild erfolgreich hochgeladen.',
        'image_url' => $fullPath,
        "img_x" => $width,
        "img_y" => $height,
        "debug_filename" => $filename // Zur Kontrolle
    ]);
}


    public function save(Request $request, string $table): Response
    {
        // noch zu implementieren
        return response("save() Platzhalter", 200);
    }

    public function CopyLeft(): JsonResponse
    {
        $cl = DB::table('copyleft')->select('tag', 'name')->get();
        return response()->json([
            'message' => 'Copyleft Success',
            'copyleft' => $cl,
        ]);
    }

    public function store_json(Request $request): JsonResponse
    {
        $data = $request->images;
        $folder = $request->input('folder');
        $path = public_path($folder . '/index.json');

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, true, true);
        }

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return response()->json(['success' => true]);
    }

    public function AddJson(string $folder, string $fn): void
    {
        $jsonPath = public_path("/images/" . $folder . "/index.json");

        if (!is_file($jsonPath)) {
            if (!File::exists(dirname($jsonPath))) {
                File::makeDirectory(dirname($jsonPath), 0777, true, true);
            }
            file_put_contents($jsonPath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $oldjs = file_get_contents($jsonPath);
        $array = json_decode($oldjs) ?? [];

        $size = @getimagesize(public_path("images/" . $folder . "/big/" . $fn));
        $w = $size[0] ?? 0;
        $h = $size[1] ?? 0;

        $lastPosition = 0;
        if (!empty($array)) {
            $last = end($array);
            $lastPosition = $last->position ?? 0;
        }
        $filenames = array_map(fn($item) => $item->filename, $array);

        if (!in_array($fn, $filenames)) {
            $array[] = (object)[
                'position' => $lastPosition + 1,
                'filename' => $fn,
                'label' => '',
                'width' => $w,
                'height' => $h,
            ];
        }

        file_put_contents($jsonPath, json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function Del_Image(string $column, string $folder, int $posi = 1): JsonResponse
    {
        if (!CheckRights(Auth::id(), CleanTable(), "delete")) {
            return response()->json(["success" => "false", "message" => "Rechte nicht vorhanden"]);
        }
        $path = public_path("/images/_" . SD() . "/images/" . $column . "/" . $folder . "/");
        $oldjs = file_get_contents($path . "index.json");
        $oldjs = json_decode($oldjs, true);
        $newjs = $this->deleteImageByPosition($oldjs ?? [], ($posi + 1), $path);
        file_put_contents($path . "index.json", json_encode($newjs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return response()->json(["success" => "true", "message" => "Bild wurde erfolgreich gelöscht"]);
    }

    protected function deleteImageByPosition(array $images, int $deletePosition, string $path): array
    {
        if (count($images) === 1 && ($images[0]['position'] ?? null) == $deletePosition) {
            $file = $images[0]['filename'];
            @unlink($path . "thumbs/" . $file);
            @unlink($path . "orig/" . $file);
            @unlink($path . "big/" . $file);
            @unlink($path . $file);
            file_put_contents($path . "index.json", "[]");
            return [];
        }

        $images = array_filter($images, function ($item) use ($deletePosition, $path) {
            if (($item['position'] ?? null) === $deletePosition) {
                $file = $item['filename'];
                @unlink($path . "thumbs/" . $file);
                @unlink($path . "orig/" . $file);
                @unlink($path . "big/" . $file);
                @unlink($path . $file);
                return false;
            }
            return true;
        });

        $images = array_values($images);
        foreach ($images as $index => &$item) {
            $item['position'] = $index + 1;
        }
        return $images;
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            if (!$file->isValid()) {
                return back()->withErrors(['photo' => 'Upload fehlgeschlagen.']);
            }

            $host = $request->getHost();
            $subdomain = SD();
            $folder = public_path("images/_{$subdomain}/users/profile_photo_path");

            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0777, true);
            }

            if (!empty($user->profile_photo_path)) {
                $oldPath = $folder . DIRECTORY_SEPARATOR . $user->profile_photo_path;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $filename = md5($file->getClientOriginalName() . "_" . Auth::id()) . "." . $file->getClientOriginalExtension();
            $file->move($folder, $filename);
            $user->profile_photo_path = $filename;
        }

        $user->name = $request->input('name');
        if ($request->has('first_name')) {
            $user->first_name = $request->input('first_name');
        }
        $user->email = $request->input('email');
        $user->save();

        return redirect()->back()->with('success', 'Bild erfolgreich aktualisiert');
    }

    public function getProfilePhoto(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'path' => $user->profile_photo_path
                ? asset("images/_" . SD() . "/users/profile_photo_path/" . $user->profile_photo_path)
                : asset('images/default_profile.png'),
        ]);
    }
}

