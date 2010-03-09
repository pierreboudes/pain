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
require_once('../authentication.php'); 
if (isset($_POST['login'])) {
   phpCAS::forceAuthentication();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">

<head>
<title>Emploi du temps L2 info</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- FIREBUG DU PAUVRE !
<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
-->

<script type='text/javascript' src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/ui.core.js"></script>
<script type="text/javascript" src="../js/ui.draggable.js"></script>
<script type="text/javascript" src="../js/ui.droppable.js"></script>
<link rel='stylesheet' media='all' href='edt.css' type='text/css' />
<link rel='stylesheet' media='print' href='edt_print.css' type='text/css' />

<script type="text/javascript" src="edt.js">
</script>

<meta name="description" content="Système de gestion des services du département d'informatique" />

</head>

<body>
<div id="edt" ondblclick="sauvegardercontenu();">
<?php  require_once('act_charger.php'); ?>
</div>
<?php if (phpCAS::isAuthenticated()): ?>
<p>
<div id="logout">
  <a href="logout.php">Logout</a>
</div>
</p><p>
<div id="enregistrer">
  <fieldset>
  <legend>Enregistrer cette version de l'emploi du temps</legend>
  <label for="message">Message (nécessaire) : </label>
  <input type="hidden" name="login" value="<?php echo phpCAS::getUser(); ?>"></input>
  <input type="text" name="message"></input>
  <button onClick="enregistrer()">
  Enregistrer
  </button>
 </fieldset>
</div>
</p>
<?php else: ?>
<p>
<div id='login'>
  <form name="login" action="" method="post">
  <input type="hidden" name="login" value="login"/>
  <input type="submit" name="dologin" value="login"/>
  </form>
</div>
</p>
<?php endif; ?>
<p>
<div id="histo">
    <fieldset>
  <legend>Historique des versions</legend>
<div id="historique">
  <?php require_once('act_historique.php'); ?>
</div>
</fieldset>
</div>
</p>
</body>
</html>