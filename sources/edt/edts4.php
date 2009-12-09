<?php require_once('authentication.php'); 
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

<script type='text/javascript' src="../jquery.js"></script>
<script type="text/javascript" src="../jquery/ui.core.js"></script>
<script type="text/javascript" src="../jquery/ui.draggable.js"></script>
<script type="text/javascript" src="../jquery/ui.droppable.js"></script>
<style type="text/css">
body {font-size: 14px;}
.jour, #histo, #enregistrer {width:802px}
.jour {clear: both; border: 1px solid white; padding: 0px;
height: 102px;; background: black;}
.creneau {float: left; width: 120px; height: 100px; background: gray;
color: white; padding: 1px;}
.nomjour {float: left; width: 60px; padding-right: 10px; padding-top: 1px; background:
black; color: white; text-align: right;}
.midi, .soir {background: silver;}
.cours {color: black; align: center; width: 116px; height: 23px;
background: silver; padding: 2px; font-size: 14px; border:
1px solid silver; margin-left: -1px;  margin-bottom: -1px;}
.CM {height: 75px;}
.TD {height: 16px;}
.TP {height: 11px;}
.POO {background: #00D8D9;}
.AA {background: #FDFDAD;}
.SR {background: #FFABD4;}
.GL {background: #ABD6FE;}
.maths {background: #FFD5AB;}
.anglais {background: #CEF7D1;}  
.sport {background: #D4FED7;}
.annuler {background-image: url(annuler.png);}
  </style>
 
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
  <input type="submit" name="login" value="login"/>
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