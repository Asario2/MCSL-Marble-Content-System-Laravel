<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Settings;
use Carbon\Carbon;

class RssController extends Controller
{
    public function feed()
    {
        $tables = DB::table('admin_table')
            ->where('xkis_feedable', 1)
            ->pluck('name');

        $items = [];

        foreach ($tables as $table) {

            $rssFields = Settings::$rss_fields[$table] ?? null;
            if (!$rssFields || count($rssFields) < 2) continue;

            $headlineField = $rssFields[0];
            $textField = $rssFields[1];

            $hasImage = Schema::hasColumn($table, 'image_path');

            $rows = DB::table($table)
            ->select($table.'.*')
            ->when(Schema::hasColumn($table, 'pub'), function ($q) {
                $q->where('pub', 1);
            })
            ->whereNotNull('created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();


            foreach ($rows as $row) {
                if(@$row->image_categories_id)
                {
                    $row->slug = DB::table("image_categories")->where("id",$row->image_categories_id)->value('slug')."?search=".$row->created_at;
                    $table2 = "home/show/pictures";
                    $head  = "Neues Bild auf <a href='https://www.asario.de/$table2/$row->slug'>https://www.asario.de</a>";
                }
                elseif(@$row->autoslug)
                {
                    $row->slug = $row->autoslug;
                    $table2 = $table."/show";
                    $head  = "Neuer Blog Eintrag auf <a href='https://www.asario.de/$table2/$row->slug'>https://www.asario.de</a>";
                }
                else{
                    $row->slug = "?search=".$row->created_at;
                    $table2 = "home/".$table;
                    $head  = "Neue $table auf <a href='https://www.asario.de/$table2/$row->slug'>https://www.asario.de</a>";
                }
                $items[] = [
                    'table' => $table,
                    'headline' => $row->{$headlineField} ?? '',
                    'head' => $head,
                    'text' => strip_tags($row->{$textField},"<br><h2><b><strong><i><em>") ?? '',
                    'image' => $hasImage ? ($row->image_path ?? null) : null,
                    'created_at' => $row->created_at,
                    'link' => url("/{$table2}/{$row->slug}"),
                    'impath' => "/images/_ab/{$table}/image_path",
                ];
            }
        }

        usort($items, function ($a, $b) {
            return Carbon::parse($b['created_at'])->timestamp
                <=> Carbon::parse($a['created_at'])->timestamp;
        });

        $items = array_slice($items, 0, 5);
        $rss = $this->buildRss($items);
        $rss = $this->beautifyXml($rss);

        return response($rss, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
    function beautifyXml(string $xml): string
    {
        libxml_use_internal_errors(true);

        // NACKTE & absichern
        $xml = preg_replace('/&(?!amp;|lt;|gt;|quot;|apos;)/', '&amp;', $xml);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        if (!$dom->loadXML($xml)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new \Exception($errors[0]->message);
        }

        return $dom->saveXML();
    }


    private function buildRss($items)
    {
        $feedTitle = "Asarios Blog";
        $feedLink  = url('/');
        $feedDesc  = "Die 5 neuesten Beiträge aus allen Bereichen";

        $rss = '<?xml version="1.0" encoding="UTF-8"?>';
        $rss .= '<rss version="2.0">';
        $rss .= '<channel>';
        $rss .= "<title>{$feedTitle}</title>";
        $rss .= "<link>{$feedLink}</link>";
        $rss .= "<description>{$feedDesc}</description>";

        foreach ($items as $item) {

            $headline = $item['headline'];
            $text = $item['text'];

            $content = '';

            if ($item['image']) {
                $content .= "<img src=\"https://asario.de{$item['impath']}/{$item['image']}\" alt=\"\" /><br/>";
            }

            $content .= "<h2>{$headline}</h2>";
            $content .= "<p>{$text}</p>";

            $rss .= '<item>';
            $rss .= "<title>{$headline}</title>";
            $rss .= "<link>{$item['link']}</link>";
            $rss .= "<pubDate>" . date(DATE_RSS, strtotime($item['created_at'])) . "</pubDate>";
            $rss .= "<description><![CDATA[<h2>{$item['head']}</h2>{$content}]]></description>";
            $rss .= '</item>';
        }

        $rss .= '</channel>';
        $rss .= '</rss>';

        return $rss;
    }
}
