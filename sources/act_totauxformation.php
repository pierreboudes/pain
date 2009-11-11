<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

$id = 2;

if (isset($_POST["id_formation"])) {
    $id = postclean("id_formation");
} 

$r=htdformation($id);
$servi = $r["servi"];
$libre = $r["libre"];
$annule = $r["annule"];
?>
<img class="imgbarre" src="act_barre.php?servi=<?=$servi?>&libre=<?=$libre?>&annule=<?=$annule?>" title="<?php ig_htd($r); ?>"/>
