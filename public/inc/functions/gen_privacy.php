<?php

function gen_privacy()
{
    TitleName("Datenschutzerkl&auml;rung");
    include_once "inc/functions/rinfo_num.php";
    include_once "inc/functions/rinfo_code.php";
    include_once "inc/functions/gen_cont_info.php";
    include_once "inc/functions/gen_hiddate.php";
    include_once "inc/functions/gen_PiwikLinkActive.php";
    global $dbg,$ln;
    rinfo_num(1);

    $xx = "";
    $xc = "";
    $id = 1;
    $id2  = 0;
           $hassl = ($ln != "de" ? "_".$ln : '');

           $dbg->DoSql("SELECT headline".$hassl.",message".$hassl.",date,shortz FROM ffrog_rinfo WHERE xico_doms LIKE '%".$_SESSION['ge']."%' ".pwe('',1)." GROUP BY ordering DESC");
           while($row = $dbg->GetRes())
           {
            $row['headline'] = (@$row['headline']);
            $row['message'] = (@$row['message']);

            //$xc .= "<li class='priv_li'>".$id. ". <a href='#priv_$id2'>" .$row['headline'.$hassl]."</a></li>";
            $xx .= "<div class='row' id='".$row['shortz']."'>
                        <div class='twelve columns hide-for-small'>
                        <article>
                        <br><h4 class='priv'>".rinfo_num()."".$row['headline'.$hassl]."</h4>

                        ".getresult(str_replace(array("fx_gen_PiwikLinkActive()","fx_gen_cont_info()"),array(gen_PiwikLinkActive(),gen_cont_info()),rinfo_code(stripcslashes(nl2br($row['message'.$hassl])))))."

                        </article>
                        </div>
                        </div>
                        <div class='row'>
                        <div class='twelve columns show-for-small' style='max-width:100%;'>
                        <br><h4 class='priv'>".rinfo_num('',1)."".$row['headline'.$hassl]."</h4>

                        ".getresult(str_replace(array("fx_gen_PiwikLinkActive()","fx_gen_cont_info()"),array(gen_PiwikLinkActive(),gen_cont_info()),rinfo_code(stripcslashes(nl2br($row['message'.$hassl])))))."

                        </div>
                        </div>";

                        $id++;
                         $id2++;
                       // $xx .= gen_hiddate($row['date']);
            }
$xx .= "
<br>    ";
return $xx;
}

?>
