<?php

function rinfo_num($es='',$ex='0')
{

global $tttt;
if($es)
{
    $tttt = array();
    return;
}
$xx = @$tttt[$ex].". ";
$tttt[$ex]= @($tttt[$ex]+1);
if($xx == ". ")
{
    return;
}
return $xx;
}
?>
