<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Settings;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class CountPixelController extends Controller
{
    protected $excludeRoutes = [
        'admin.*',
        '*statistics*',
        'cookieconsent.*',
        '*stats*',
        'tables.noview',
    ];

    protected $excludeURLs = [
        '*.php*',
        '*.js*',
        '*.css*',
        '*.png*',
        '*.jpg*',
        '*.jpeg*',
        '*.svg*',
        '*.webp*',
        '*.gif*',
        '*.ico*',
    ];
    public static array $nostats = [
    '/admin',
    '/_debug',
    '/api/',
    "/login",
    "pm/index",
    ];

    public function track(Request $request)
    {
        // if(!substr_count(@$_COOKIE['mcsl_preferences'],'"analytics":true'))
        // {
        //         return $this->pixelResponse();
        // }
        try {

            $host = $request->getHost();
            if ($host === 'mail.marblefx.net') {
                return $this->pixelResponse();
            }


            $routeName = $request->query('route') ?? 'unknown';

            /**
             * 0️⃣ Leere, ungültige oder 404-Routen ausschließen
             */
            if (
                $routeName === null ||
                $routeName === '' ||
                $routeName === 'unknown' ||
                Route::getRoutes()->getByName($routeName) === null
            ) {
                return $this->pixelResponse();
            }

            /**
             * 1️⃣ Route anhand der Muster ausschließen
             */
            foreach ($this->excludeRoutes as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    return $this->pixelResponse();
                }
            }

            /**
             * URL bereinigen
             */
            $rawUrl = $this->SH($request->query('url')) ?? '/';

            /**
             * 2️⃣ URL anhand Dateiendungen ausschließen
             */
            foreach ($this->excludeURLs as $pattern) {
                if (fnmatch($pattern, $rawUrl)) {
                    return $this->pixelResponse();
                }
            }

            /**
             * 3️⃣ Prüfen ob Route Auth-Middleware besitzt
             */
            $routeAction = Route::getRoutes()->getByName($routeName)?->action ?? [];
            $middlewares = $routeAction['middleware'] ?? [];
            $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

            if (in_array('auth', $middlewares)) {
                return $this->pixelResponse();
            }

            /**
             * 4️⃣ IP anonymisieren
             */
            $ip = $request->ip();

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $parts = explode('.', $ip);
                $parts[3] = '0';
                $ip = implode('.', $parts);
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $parts = explode(':', $ip);
                $parts[count($parts) - 1] = '0000';
                $ip = implode(':', $parts);
            }

            /**
             * 5️⃣ Tracking speichern
             */
            PageView::create([
                'dom'        => SD(),
                'url'        => $rawUrl,
                'ip'         => $ip,
                'visited_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error("CountPixel DB Error: " . $e->getMessage());
        }

        return $this->pixelResponse();
    }

    protected function pixelResponse()
    {
        $pixel = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII='
        );
        return response($pixel)->header('Content-Type', 'image/png');
    }

    public function SH($str)
    {
        if (!$str) return '/';

        $decoded = rawurldecode($str);
        $clean = str_replace(
            [request()->getHost(), "https://", 'www.', "http://"],
            '',
            $decoded
        );

        return empty($clean) ? '/' : $clean;
    }

    public function dboard(Request $request)
    {


        //
        // 1) Domain bestimmen


        $domm = strtolower($request->dom);
        $m = max((int) $request->month, 1);

        $ris  = ($domm !== '');

        if (!$ris) {
            $domm = strtolower(SD());
        }

        Log::info("REQUEST DOM = {$domm}, RIS = " . ($ris ? 'true' : 'false'));

        //
        // 2) Query Grundstruktur
        //
        $query = DB::connection('gnerals')
            ->table('page_views');
        $query->where('visited_at', '>=', now()->subMonths($m));

        //
        // 3) Rechte-Logik
        //
        // Wenn KEINE Rechte: immer eigene Domain erzwingen
        // Wenn GET dom gesetzt wurde: dom explizit erzwingen
        //
        if (!@CheckZRights("StatisticsAll") || $ris) {
            $query->whereRaw("LOWER(dom) = ?", [$domm]);
            Log::info("WHERE applied: dom = {$domm}");
        } else {
            Log::info("NO WHERE applied – full stats allowed");
        }

        //
        // 4) Daten holen
        //
        $rows = $query
            ->select('url', DB::raw('LOWER(dom) as dom'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('url', 'dom')
            ->orderBy('url')
            ->get();

            $rows = $rows->filter(function ($row) {
                foreach (Settings::$nostats as $ignore) {
                    if ($ignore !== '' && str_contains($row->url, $ignore)) {
                        return false; // URL ignorieren
                    }
                }
                return true;
            })->values();


        Log::info("ROWS DOMS = " . json_encode($rows->pluck('dom')->unique()->values()->all()));

        //
        // 5) Labels sammeln
        //
        $labels = $rows->pluck('url')->unique()->values()->all();

        //
        // 6) Farben pro Domain
        //
        $domColors = [
            'ab'  => '#4F86F7',
            'mfx' => '#FFA500',
            'dag' => '#E63946',
            'chh' => '#1B3A8A',
        ];

        //
        // 7) Welche Domains sollen dargestellt werden?
        //
        if (!CheckZRights("StatisticsAll")) {
            // Nur SD()
            $doms = [ strtolower(SD()) ];
        } elseif ($ris) {
            // Nur die gefilterte Domain
            $doms = [ $domm ];
        } else {
            // Alle in den Ergebnissen
            $doms = $rows->pluck('dom')->unique()->values()->all();
        }

        Log::info("Final DOMS to display = " . json_encode($doms));

        //
        // 8) Dataset erzeugen
        //
        $datasets = [];

        foreach ($doms as $dom) {

            $label = SD('1', $dom);
            $color = $domColors[$dom] ?? '#888888';

            $data = array_fill(0, count($labels), 0);

            foreach ($rows as $r) {
                if ($r->dom === $dom) {
                    $idx = array_search($r->url, $labels);
                    if ($idx !== false) {
                        $data[$idx] = (int)$r->cnt;
                    }
                }
            }

            $datasets[] = [
                'label' => $label,
                'data'  => $data,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'borderWidth'     => 1,
            ];
        }

        //
        // 9) Ausgabe
        //
        return response()->json([
            'labels'   => $labels,
            'datasets' => $datasets,
        ]);
    }


    public function stats()
    {
        $data = DB::connection('gnerals')
            ->table('page_views')
            ->select('url', DB::raw('COUNT(*) as views'))
            ->groupBy('url')
            ->orderBy('views', 'DESC')
            ->get();

        return response()->json($data);
    }
}
