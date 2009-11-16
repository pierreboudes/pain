<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009 Pierre Boudes, département d'informatique de l'institut Galilée.
 *
 * This file is part of Pain.
 *
 * Pain is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pain is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pain.  If not, see <http://www.gnu.org/licenses/>.
 */
header('Content-type: image/png');


$servi = 0;
$libre = 0;
$annule = 0;
$mutualise = 0;

if (isset($_GET["servi"])) {
    $servi = $_GET["servi"];
}

if (isset($_GET["libre"])) {
    $libre = $_GET["libre"];
}

if (isset($_GET["annule"])) {
    $annule = $_GET["annule"];
}

if (isset($_GET["mutualise"])) {
    $mutualise = $_GET["mutualise"];
}

$largeur = $servi+$libre+$annule+$mutualise;

if($largeur > 0): /****** faire une vraie barre ******/

$echelle = round(880.0 / $largeur, 1); /* si PHP 5.3.0 ou plus paramètre supplémentaire :
					PHP_ROUND_HALF_DOWN */

if ($echelle > 1) {
  $echelle = 1;
}


$servi *= $echelle;
$libre *= $echelle;
$annule *= $echelle;
$mutualise *= $echelle;

$largeur = $servi+$libre+$annule+$mutualise;

$hauteur = 7;

if (isset($_GET["hauteur"])) {
    $hauteur = $_GET["hauteur"];
}

$im = imagecreatetruecolor($largeur,$hauteur);
$fondcolor = imagecolorallocate($im, 255, 255, 255);
/* ne tourne pas au lipn imageantialias($im, true); */
imagefilledrectangle($im, 0, 0, $largeur, $hauteur, $fondcolor);
$white = imagecolorallocate($im,255,255,255);
$rayurecolor = imagecolorallocate($im, 230, 230, 230);
$servicolor = imagecolorallocate($im, 58, 105, 35); /* vert */
$librecolor = imagecolorallocate($im, 175, 29, 30); /* rouge */
$annulecolor = imagecolorallocate($im, 255, 144, 0); /* orange */
$mutualisecolor = imagecolorallocate($im, 100, 149, 237); /* bleu cadre */
$x1 = 0;
$x0 = $x1;
$x1 += $servi;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $servicolor);

$x0 = $x1;
$x1 += $mutualise;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $mutualisecolor);

$x0 = $x1;
$x1 += $libre;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $librecolor);

$x0 = $x1;
$x1 += $annule;
imagefilledrectangle($im, $x0, 0, $x1, $hauteur, $annulecolor);

imagestring($im, 1, 1, -1, $echelle."px = 1H", $white);

else: /****** largeur nulle, on renvoie un image transparente 1x1 ******/
$im = imagecreatetruecolor(1,1);
$fondcolor = imagecolorallocatealpha($im, 255, 255, 255,255);
/* ne tourne pas au LIPN imageantialias($im, false); */
imagefilledrectangle($im, 0, 0, 1, 1, $fondcolor);

endif; /* on renvoie l'image */

imagepng($im);
imagedestroy($im);
?>
