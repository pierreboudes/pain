<?php /* -*- coding: utf-8 -*-*/
header('Content-type: image/png');


$servi = 0;
$libre = 0;
$annule = 0;

if (isset($_GET["servi"])) {
    $servi = $_GET["servi"];
}

if (isset($_GET["libre"])) {
    $libre = $_GET["libre"];
}

if (isset($_GET["annule"])) {
    $annule = $_GET["annule"];
}

$echelle = 1;

$servi *= $echelle;
$libre *= $echelle;
$annule *= $echelle;

$largeur = $servi+$libre+$annule;

$hauteur = 7;

if (isset($_GET["hauteur"])) {
    $hauteur = $_GET["hauteur"];
}



if($largeur >0):
$im = imagecreatetruecolor($largeur,$hauteur);
$fondcolor = imagecolorallocate($im, 255, 255, 255);
imageantialias($im, true);
imagefilledrectangle($im, 0, 0, $largeur, $hauteur, $fondcolor);
$white = imagecolorallocate($im,255,255,255);
$rayurecolor = imagecolorallocate($im, 230, 230, 230);
$servicolor = imagecolorallocate($im, 58, 105, 35);
$librecolor = imagecolorallocate($im, 175, 29, 30); 
$annulecolor = imagecolorallocate($im, 255, 144, 0); 
$x0 = 0;
$x1 = $servi;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $servicolor);
//imagestring($im, 2, $x0 + 1, -2, "servi", NULL);
$x0 = $x1;
$x1 += $libre;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $librecolor);
//imagestring($im, 1, $x0 + 1, 0, "libre",$white);
$x0 = $x1;
$x1 += $annule;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $annulecolor);
//imagestring($im, 1, $x0 + 1, 0, "annulé", $white);
/* for ($i = -$hauteur; $i < $largeur; $i = $i + 3) {
    imageline($im, $i + $hauteur, -1, $i, $hauteur+1, $rayurecolor);
    } */
else: 
/* largeur nulle, on renvoie un image transparente 1x1 */
$im = imagecreatetruecolor(1,1);
$fondcolor = imagecolorallocatealpha($im, 255, 255, 255,255);
imageantialias($im, false);
imagefilledrectangle($im, 0, 0, 1, 1, $fondcolor);
endif;
imagepng($im);
imagedestroy($im);
?>