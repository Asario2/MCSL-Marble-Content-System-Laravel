<?php

function gen_PiwikLinkActive()
{
global $cfg,$ln,$lang;
$cfg['compdom3'] = "https://www.asario.de";
if(!substr_count_alt($cfg['compdom'],"localhost.d"))
{
    $www = "www.";
    $s =  's';
}

if(!@substr_count_alt(@$_COOKIE['cookieControlPrefs'],"analytics")){

    return $lang['cookie_off']."<br />
            ".$lang['cookie_noti']."
           <a href='javascript:void(0);' onclick=\"ck_show()\">".$lang['cookie_link']."</a>.";
}
if(substr_count_alt($_SERVER['HTTP_HOST'],"localhost"))
{
    $dom = $cfg['compdom3']."/";
}
else
{
    $dom = BP();
}

return "<iframe style='border: 0; height: 200px; width: 100%;background-color: transparent; color: white;' frameborder='0' align='top' src='".$dom."module/mod_PiwiX/index.php?module=CoreAdminHome&action=optOut&idsite=".$cfg['piwik_id']."&language=$ln'></iframe>";
}
?>
