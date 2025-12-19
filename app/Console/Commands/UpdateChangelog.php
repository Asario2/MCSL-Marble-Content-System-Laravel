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
        $configFile = 'D:\XAMPPP\htdocs\laravel-tutorial\oliver-rein\config\starter_eleven.php';
        $readmeFile = 'E:\git\git-olirein\README.md';

        $allIssues = collect();
        $page = 1;
        $perPage = 100;

        $this->info("📥 Lade geschlossene Issues von GitHub...");

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

            // Filter: keine PRs & nicht geplante/duplizierte
            $issues = collect($response->json())->reject(fn($issue) => !empty($issue['pull_request']))
                ->filter(function ($issue) {
                    $labels = collect($issue['labels'])->pluck('name')->map(fn($l) => strtolower($l));
                    return !$labels->contains('not planned') && !$labels->contains('duplicate');
                });

            $allIssues = $allIssues->merge($issues);
            $this->info("➡️ Gefundene Issues auf Seite {$page}: {$issues->count()}");

            $page++;
        } while ($issues->count() === $perPage);

        $this->info("✅ Insgesamt gefundene Issues: {$allIssues->count()}");

        // Nach closed_at sortieren (älteste zuerst)
        $allIssues = $allIssues->sortBy('closed_at')->values();

        // CHANGELOG einlesen
        $changelog = file_exists($changelogPath) ? file_get_contents($changelogPath) : '';

        // Bereits vorhandene Issue-IDs suchen
        preg_match_all('/\(#(\d+)\)/', $changelog, $existingMatches);
        $existingIds = collect($existingMatches[1] ?? [])->map(fn($id) => (int)$id);

        // Nur neue Issues
        $newIssues = $allIssues->reject(fn($issue) => $existingIds->contains((int)$issue['number']));
        if ($newIssues->isEmpty()) {
            $this->info("ℹ️ Alle Issues sind bereits im Changelog vorhanden.");
            return Command::SUCCESS;
        }

        // Letzte Version im Changelog auslesen
        preg_match_all('/version-(\d+)\.(\d+)\.(\d+)/', $changelog, $versionMatches);
        $versionArray = $versionMatches[0] ?? [];
        $lastVersion = !empty($versionArray) ? end($versionArray) : null;

        if ($lastVersion) {
            preg_match('/(\d+)\.(\d+)\.(\d+)/', $lastVersion, $matches);
            $major = (int)($matches[1] ?? 2);
            $minor = (int)($matches[2] ?? 4);
        } else {
            $major = 2;
            $minor = 4;
        }

        $entries = '';
        $lastVersionUsed = '';

        foreach ($newIssues as $issue) {
            // Minor/major hochzählen
            if ($minor >= 99) {
                $major++;
                $minor = 0;
            } else {
                $minor++;
            }
            $patch = rand(0, 9);

            $version = sprintf("%d.%02d.%d", $major, $minor, $patch);
            $lastVersionUsed = $version;
            $badge = "![Version](https://img.shields.io/badge/version-{$version}-orange)";

            $entries .= "{$badge} {$issue['title']} (#{$issue['number']})  \n";
        }

        // Neue Sektion unten anhängen
        $newChangelog = $changelog . "" . $entries;
        file_put_contents($changelogPath, $newChangelog);
        $this->info("📄 Alle neuen Issues nach {$changelogPath} geschrieben.");

        // ✅ Config aktualisieren
        if (file_exists($configFile)) {
            $configContent = file_get_contents($configFile);
            $configContent = preg_replace(
                "/('versionnr'\s*=>\s*)['\"].*?['\"]/",
                "\$1'{$lastVersionUsed}'",
                $configContent
            );
            $configContent = preg_replace(
                "/('versionsdatum'\s*=>\s*)['\"].*?['\"]/",
                "\$1'" . date('d.m.Y') . "'",
                $configContent
            );
            file_put_contents($configFile, $configContent);
            $this->info("⚙️ config/starter_eleven.php aktualisiert (Version/Datum)");
        }

        // ✅ README.md aktualisieren
        if (file_exists($readmeFile)) {
            $readmeContent = file_get_contents($readmeFile);
            $readmeContent = preg_replace(
                "/!\[Version\]\(https:\/\/img\.shields\.io\/badge\/version-.*?-orange\)/",
                "![Version](https://img.shields.io/badge/version-{$lastVersionUsed}-orange)",
                $readmeContent,
                1
            );
            file_put_contents($readmeFile, $readmeContent);
            $this->info("📘 README.md Badge aktualisiert auf Version {$lastVersionUsed}");
        }

        return Command::SUCCESS;
    }

    public function handle_getallnew()
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
