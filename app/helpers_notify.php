<?php
if(!function_exists("DUMP_DB"))
{
    function DUMP_DB()
    {
        $batchFilePath = "D:\\XAMPPP\\htdocs\\mysql_bgu\\bats\\GetAllOnlines.bat";
        if (file_exists($batchFilePath)) {
            // Execute the batch file
            $output = shell_exec($batchFilePath);

            // Display output or confirmation message
            return "<pre>$output</pre>";
        } else {
            return "Batch-Datei nicht gefunden.";
        }
    }
    if(!function_exists("Notify"))
    {
        function Notify()
        {
            $entries = DB::table("notifications")->get();
            foreach($entries as $entry)
            {
                echo "<h3>".$entry['name']."</h3><br />";
                echo $entry['text'];
                if($entry['function_link'])
                {
                    $tz  = $entry['function_link'];
                    echo $tz();
                }

            }
        }
    }
}
