<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use App\Models\Settings;

class SettingsController extends Controller
{
    public function all()
    {
        return response()->json([
            'headline' => Settings::$headline,
            'exl' => Settings::$exl,
            'excl_cols' => Settings::$excl_cols,
            'excl_heads' => Settings::$excl_heads,
            'excl_disabled' => Settings::$excl_disabled,
            'excl_datefields' => Settings::$excl_datefields,
            'no_req' => Settings::$no_req,
            'big_thumb' => Settings::$big_thumb,
            'int_date_tables' => Settings::$int_date_tables,
            'textfield' => Settings::$textfield,
            'image_sizes' => Settings::$image_sizes,
            'image_paths' => Settings::$image_paths,
            'copyright_tables' => Settings::$copyright_tables,
            'copyright_marker' => Settings::$copyright_marker,
            'searchable' => Settings::$searchable,
            'statusvals' => Settings::$statusvals,
            'searchFields' => Settings::$searchFields,
            'underCals' => Settings::$underCals,
            'otherField' => Settings::$otherField,
            'impath' => Settings::$impath,
            'namealias' => Settings::$namealias,
            'descalias' => Settings::$descalias,
            "presetting"=> Settings::$presetting,
            "aftsetting"=> Settings::$aftsetting,
            "thirdparty"=> Settings::$thirdparty,
            "userexcl"=> Settings::$userexcl,
        ]);
    }
}


