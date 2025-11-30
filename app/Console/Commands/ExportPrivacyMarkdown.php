<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\VCardHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GlobalController;

class ExportPrivacyMarkdown extends Command
{
    protected $signature = 'export:privacy-markdown {--table= : Die Tabelle, die exportiert werden soll}';
    protected $description = 'Exportiert die privacy-Tabelle als Markdown-Datei mit Inhaltsverzeichnis und Anchors';

    public function handle()
    {
        GlobalController::SetDomain();
        $table = $this->option('table');
        if (!$table) {
            $this->error('Bitte die Tabelle angeben mit --table=TABELLENNAME');
            return;
        }
        $sda = $table;
        $entries = DB::table('genxlo.privacy')->where("xico_doms", "LIKE", "%" . $sda . "_mcsl%")->orderBy('position',"ASC")->get();

        if ($entries->isEmpty()) {
            $this->error('Keine Einträge in der Tabelle "privacy" gefunden.');
            return;
        }

        $markdown = "# Datenschutzerklärung\n\n## Inhaltsverzeichnis\n\n";

        // Inhaltsverzeichnis aufbauen
        $o = 1;
        foreach ($entries as $entry) {
            $anchor = $entry->slug ?? Str::slug($entry->headline);
            $markdown .= "- [$o)&nbsp; $entry->headline](#{$anchor})<br />";
            $o++;
        }

        $markdown .= "\n---\n\n";

        // Abschnitte
        $i = 1;
        foreach ($entries as $entry) {
            $anchor = $entry->slug ?? Str::slug($entry->headline);

            $markdown .= "<a id=\"{$anchor}\"></a>\n";
            $markdown .= "## <span class='dark:text-layout-night-1050 text-layout-sun-1000'>$i) {$entry->headline}</span>\n\n";
            $markdown .= nl2br($this->convertToMarkdown($entry->message)) . "\n\n";
            $markdown .= "---\n\n";
            // $markdown = $this->nobsp($markdown);
            $i++;
        }

        // Datei speichern
        Storage::disk('md')->put('privacy_'.$sda.'.md', $markdown);
        $this->info("Markdown-Datei wurde unter storage/app/privacy_".$sda.".md gespeichert.");
    }
    protected function convertToMarkdown(string $message): string
    {
        // Optional: HTML zu Markdown konvertieren
        // Hier sehr einfach gehalten – kann bei Bedarf z. B. mit `league/html-to-markdown` ersetzt werden

        $text = strip_tags($message, '<br><ul><ol><li><strong><h3><h4><h2><em><b><i><a>');
        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $text);
        $text = str_replace("{{ vcard }}",$this->vcard(),$text);

        return $text;
    }
    function vcard()
    {
        $data = DB::table("genxlo.kontaktdaten")->where("dom",SD())->first();
        $xx = VCardHelper::buildVCard((array) $data);


    return $xx;
    }
}
