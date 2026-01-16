<?php

function gen_cont_info($dom2='')
{
global $lang,$dbgg,$pref;
$dom = $_SESSION['ge'];
include_once "inc/functions/gen_hidemail.php";
include_once "inc/functions/smilies_oxx.php";


$sql = "SELECT * FROM ffrog_kontaktdaten WHERE dom='$dom' LIMIT 1";
$dbgg->DoSql($sql);
$row = $dbgg->GetRes();

$name = @$row['name'];
$adr = @$row['strasse'];
$plz = @$row['plz'];
$ort = @$row['ort'];
$region = @$row['bundesland'];
$tel = @$row['festnetz'];
$mobil = @$row['mobil'];
$mob  = $mobil ? $lang['mobil'].": <a href='tel:".$mobil."'>$mobil</a><br />" : '';
$email = @gen_hidemail(@$row['email']);
$fax = (@$row['fax'] ? @$lang['fax'].": ".$row['fax']."<br />" : '');
return smilies_oxx("<div class=\"vcard\">
        <address><br />
        <div class='subheader'>
         <span class=\"fn\">$name</span><br />
         <span class=\"adr\">$adr<br />
         <span class='postalCode'>$plz</span> <span class=\"locality\">$ort</span><br />
         ".@$lang['phone'].": <a href='tel:".$tel."'>$tel</a><br />".
         @$mob.
         $fax."
         ".@$lang['email'].": $email<br /><br />
         </div>
         </address>
         </div>");

}
?>
