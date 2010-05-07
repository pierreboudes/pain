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
 En cas de problème d'affichage : recharger la page.</b></p>

<p><b>Attention, des travaux sont en cours pour le passage à une nouvelle version de pain</b> (<a href="../campagne/">campagne</a>), certaines données, notamment les statistiques, peuvent devenir erronées dans la version que vous visitez actuellement.</p>
<p><b>Attention, le <a href="http://www-lipn.univ-paris13.fr/~boudes/wikka/wikka.php?wakka=NoticeServices2009">wiki</a> fermera avant juillet 2010.</b> Les informations sur les services du wiki ne doivent plus être considérées comme étant à jour.</p>
<p>La  <a href="https://www-lipn.univ-paris13.fr/projects/licences/wiki/AccueilPain" title="Accueil pain">page web de pain</a> contient des informations complémentaires. Un système de tickets, pour signaler des bugs ou demander des améliorations, y est disponible (vérifier que le bug n'est pas déjà signalé dans un ticket avant de créer un nouveau ticket).</p>
</div>
</center>
