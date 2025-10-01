<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateChangelog extends Command
{
    protected $signature = 'changelog:update';
    protected $description = 'Lädt alle geschlossenen GitHub-Issues (ohne PRs), versioniert sie nach closed_at und schreibt sie in die CHANGELOG.md';

    public function handle()
    {
        $owner = "Asario2";
        $repo = "MCSL-based-on-Starter-Eleven";
        $changelogPath = 'E:\git\git-olirein\CHANGELOG.md';

        $allIssues = collect();
        $page = 1;
        $perPage = 100;

        // Alle geschlossenen Issues mit Pagination abrufen
        do {
            $this->info("📥 Lade Seite {$page} mit {$perPage} Issues...");

            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Accept' => 'application/vnd.github+json'])
                ->get("https://api.github.com/repos/{$owner}/{$repo}/issues", [
                    'state' => 'closed',
                    'per_page' => $perPage,
                    'page' => $page,
                ]);

            if ($response->failed()) {
                $this->error('❌ Fehler beim Laden der GitHub-Issues');
                return Command::FAILURE;
            }

            $fetched = collect($response->json());

            // 👉 PRs direkt rausfiltern
            $issues = $fetched->filter(fn($issue) => empty($issue['pull_request'] ?? null));

            // Labels checken
            $issues = $issues->filter(function ($issue) {
                $labels = collect($issue['labels'])->pluck('name')->map(fn($l) => strtolower($l));
                return !$labels->contains('not planned') && !$labels->contains('duplicate');
            });

            $this->info("➡️ Gefundene Issues auf Seite {$page}: " . $issues->count());

            $allIssues = $allIssues->merge($issues);
            $page++;

        } while ($fetched->count() === $perPage);

        if ($allIssues->isEmpty()) {
            $this->info('ℹ️ Keine passenden Issues gefunden.');
            return Command::SUCCESS;
        }

        $this->info("✅ Insgesamt gefundene Issues: " . $allIssues->count());

        // Nach closed_at sortieren (ältestes zuerst)
        $sorted = $allIssues->sortBy('closed_at')->values();

        // Versionierung starten
        $major = 2;
        $minor = 4;
        $patch = 1;

        $entries = '';
        foreach ($sorted as $issue) {
            // Minor hochzählen
            if ($minor >= 99) {
                $major++;
                $minor = 0;
            } else {
                $minor++;
            }
            $patch = rand(0, 9);

            $version = sprintf("%d.%02d.%d", $major, $minor, $patch);
            $badge = "![Version](https://img.shields.io/badge/version-{$version}-orange)";

            $entries .= "{$badge} {$issue['title']} (#{$issue['number']})  \n";
        }

        // Ganze Changelog-Datei neu schreiben
        file_put_contents($changelogPath, $entries);

        $this->info("📄 Alle Issues nach {$changelogPath} geschrieben.");

        return Command::SUCCESS;
    }
}
