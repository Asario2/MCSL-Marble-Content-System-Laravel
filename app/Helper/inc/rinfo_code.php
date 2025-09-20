<?php
function rinfo_code($txt)
{
    global $ln,$pref,$cfg;

    @include __DIR__."/config_".SD()."_mcsl.inc.php";

    @include __DIR__."/config_general.inc.php";

    if(@$_SESSION[$pref."_dev_mod"])
    {
        $fs = "<font color='#a00'>";
        $fe = "</font>";
    }
    $ln = "de";
    if($ln == "de" || @$cfg['xis_rinfo_lang_de'])
    {

        foreach($cf['snipp'] as $key=>$val)
        {
            $o = str_replace("|",'',$key);
            @$orige = @$cf['search_term'][$o];
            $ex = (@$_SESSION[$pref."_dev_mod"] ? " ($key)($orige)" : '');
            $txt = str_replace($key,@$fs.$val.$ex.@$fe,$txt);
        }
    }
    elseif($qsasd='ASd')
    {
        foreach($cf['snipp_en'] as $key=>$val)
        {
            $o = str_replace("|",'',$key);
            @$orige = @$cf['search_term_en'][$o];
            $ex = (@$_SESSION[$pref."_dev_mod"] ? " ($key)($orige)" : '');
            $txt = str_replace($key,@$fs.$val.$ex.@$fe,$txt);
        }
    }
file_put_contents("privacy.md",$txt);
return $txt;

}
?>
