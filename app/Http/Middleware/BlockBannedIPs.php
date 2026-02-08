<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\HlogService;

class BlockBannedIPs
{
    protected HlogService $hlog;

    public function __construct(HlogService $hlog, RequestInspectionMiddleware $relog)
    {
        $this->hlog = $hlog;
        $this->relog = $relog;
    }

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Prüfen, ob die IP aktuell auf dieser Subdomain gebannt ist
        $record = DB::table('genxlo.xgen_hlog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->orderByDesc('banned_until')
            ->first();

        if ($record) {
            $until = $record->banned_until;

            // Optional: neuen Log-Eintrag erstellen, dass geblockt wurde
            $this->hlog->logHit(
                $ip,
                $request,
                $this->relog->getScore($ip,$request), // Score 0, da nur Ban-Check
                [['source'=>'banned_check','pattern'=>'active ban','value'=>'IP blocked']]
            );

            abort(403, "Request blocked. IP banned until {$until}");
        }

        return $next($request);
    }
}
