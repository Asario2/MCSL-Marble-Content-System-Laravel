<?php

function gen_hiddate($arr)
{
    if(!substr_count_alt($arr,"|||"))
    {
       $ts = $arr;
    }
    include_once "inc/functions/dtime.php";
    @list($d1,$d2,$d3,$d4) = @explode("|||",$arr);
    $ts = $d2;
    if($d1 > $d2 || substr_count_alt($d2,"["))
    {
        $ts = $d1;
    }
    if(@$d3 > $ts)
    {
        $ts = $d3;
    }
    if(@$d4 > $ts)
    {
        $ts = $d4;
    }
    if(substr_count($d1,"["))
    {
        $ts = $d2;
    }
   return "<div class='hidden'>".dtime($ts)."</div>";
}
?>
