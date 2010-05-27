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
require_once('authentication.php'); 
authrequired();
?>
<center>
<div class="infobox">
<p>Bonjour <?php echo $user["prenom"]." ".$user["nom"]; ?></p>
<p>      
 En cas de problème d'affichage : recharger la page.</p>

<p><b>Attention, la nouvelle version de pain, <a href="https://www-lipn.univ-paris13.fr/projects/licences/milestone/Campagne" title="développement de Campagne">Campagne</a></b>, est en cours de développement. Merci de signaler les erreurs ou faire vos demandes d'amélioration, en émettant un ticket : vérifier que le bug n'est pas <a href="https://www-lipn.univ-paris13.fr/projects/licences/query?group=status&amp;milestone=Campagne" title="liste de tous les tickets pour Campagne">déjà signalé dans un ticket</a> avant de <a href="https://www-lipn.univ-paris13.fr/projects/licences/newticket?milestone=Campagne" title="Créer un ticket pour le milestone Campagne">créer un nouveau ticket</a>.</p>
<p>La  <a href="https://www-lipn.univ-paris13.fr/projects/licences/wiki/AccueilPain" title="Accueil pain">page web de pain</a> contient des informations complémentaires.</p>
<p>Expérimental : <button id="basculeAide" class="aideOn">aide</button>
</p>
</div>
<?php
include('aide.html'); 
?>
</center>
