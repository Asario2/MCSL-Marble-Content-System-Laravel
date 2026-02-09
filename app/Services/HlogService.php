<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HlogService
{
    /**
     * Score ab dem gebannt wird
     */
    protected int $maxScore = 25;

    /**
     * Score ab dem gebannt wird (zusätzlich)
     */
    protected int $blockThreshold = 25;

    /**
     * Ban-Dauer in Stunden je Verstoß-Stufe
     */
    protected array $banDurations = [
        1 => 1,
        2 => 2,
        3 => 10,
        4 => 48,
        5 => 300,
        6 => 1000,
        7 => 2000,
        8 => 5000,
    ];

    /**
     * Hauptfunktion: speichert jeden Hit, berechnet violations & banned_until
     */
    public function logHit(string $ip, Request $request, int $score, array $matches): ?Carbon
{
    $now = now();

    // Zähle bisherige Verstöße (Score > 0)
    $violations = DB::table('genxlo.xgen_hlog')
        ->where('ip', $ip)
        ->where('dom', SD())
        ->where('score', '>', 0)
        ->count() + 1;
    $violations = $this->violations($request->ip());
    $violations = min($violations, 8);

    // Ban-Dauer berechnen, auch wenn bereits Ban aktiv
    $hours = $this->banDurations[$violations] ?? 10;

    $banUntil = ($score >= $this->maxScore)
        ? $now->copy()->addHours($hours)
        : $this->GetBannedUntil($ip);
    if($score < 1 && $score != -1)
    {
        return now();
    }
    if($score > $this->maxScore)
    {
        \Log::info("SC:".$score."MS:".$this->maxScore);
    DB::table('genxlo.xgen_hlog')->insert([
            'ip'           => $ip,
            'dom'          => SD(),
            'url'          => $request->fullUrl(),
            'method'       => $request->method(),
            'score'        => $score,
            'matches'      => json_encode($matches, JSON_PRETTY_PRINT),
            'agent'        => $request->userAgent(),
            'violations'   => $violations,
            'banned_until' => $banUntil,
            'created_at'   => $now,
        ]);
    }
    // Immer einen neuen Eintrag machen


    return $banUntil;
}


    /**
     * Prüft, ob eine IP aktuell gebannt ist
     */
    public function isBanned(string $ip): bool
    {
        $record = DB::table('genxlo.xgen_hlog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->orderByDesc('banned_until')
            ->first();

        return (bool) $record;
    }

    /**
     * Liefert das aktuell gültige banned_until (oder null)
     */
    public function bannedTill(string $ip): ?Carbon
    {
        $row = DB::table('genxlo.xgen_hlog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->orderByDesc('banned_until')
            ->first();

        return $row ? Carbon::parse($row->banned_until) : null;
    }

    /**
     * Anzahl aller Verstöße einer IP
     */
    public function violations(string $ip): int
    {
       $last = DB::table('genxlo.xgen_hlog')
        ->where('ip', $ip)
        ->where('dom', SD())
        ->orderByDesc('violations')
        ->select('violations')
        ->first();

        $violations = $last ? $last->violations + 1 : 1; // falls kein Eintrag existiert, start bei 1

        return $violations;
    }

    /**
     * Prüft, ob Score hoch genug für Block ist
     */
    public function shouldBlock(int $score): bool
    {
        return $score >= $this->blockThreshold;
    }

    /**
     * Löscht alte Logs (Cron)
     */
    public function cleanup(int $days = 14): int
    {
        return '';
        return DB::table('genxlo.xgen_hlog')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
    function GetBannedUntil($ip){
    $now = now();
    $viol = DB::table("genxlo.xgen_hlog")->where("ip",$ip)->where("dom",SD())->select("violations")->orderByDesc("violations")->first();
    $hours = $this->banDurations[(@$viol->violations+1)] ?? 10;
    return $now->copy()->addHours($hours);

    }
    /**
     * Optional: alle Hits einer IP abrufen
     */
    public function getHits(string $ip)
    {
        return DB::table('genxlo.xgen_hlog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->orderByDesc('created_at')
            ->get();
    }
}
