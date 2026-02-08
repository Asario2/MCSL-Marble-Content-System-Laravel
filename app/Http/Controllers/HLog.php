<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HLog extends Controller
{
    //
    public static function save($data)
    {
        // 'ip'      => $request->ip(),
        //         'url'     => $request->fullUrl(),
        //         'method'  => $request->method(),
        //         'score'   => $score,
        //         'matches' => $matches,
        //         'agent'   => $request->userAgent(),
        DB::table('genxlo.xgen_hlog')->insert([
            'ip'         => (string) $data['ip'],
            'url'        => (string) $data['url'],
            'method'     => (string) $data['method'],
            'score'      => (int) $data['score'],
            'matches'    => is_array($data['matches']) ? json_encode($data['matches'],JSON_PRETTY_PRINT) : $data['matches'],
            'agent'      => (string) $data['agent'],
            'created_at' => now(),
        ]);

    }
    public function show()
    {
        if(!CheckZrights("Hlog"))
        {
            header("Location: /no-rights");
            exit;
        }
        $data = DB::table("genxlo.xgen_hlog")->get();
        $data->createed_at = date("d.m.Y H:i:s",strtotime($data->created_at));
        return Inertia::render('Admin/Hlog', [
            "data"=>$data,
        ]);
    }
}
