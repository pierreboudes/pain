<?php /* -*- coding: utf-8 -*- */
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

/* gestion des droits (temporaire) */

function estguest() {
    if (trim($_SERVER['PHP_AUTH_USER']) === "guest") {
	return true;
    }
    return false;
}

function estdepartement() {
    if (trim($_SERVER['PHP_AUTH_USER']) === "departement") {
	return true;
    }
    return false;
}

function peuteditercours($id_cours) {
    return estdepartement();
}

function peuteditercoursdelaformation($id_formation) {
    return estdepartement();
}

function peuteditertranche($id_tranche) {
    return estdepartement();
}

function peuteditertrancheducours($id_cours) {
    return estdepartement();
}

function peutediterformation($id_formation) {
    return estdepartement();
}

function peutediterenseignant($id_enseignant = 0) {
    return estdepartement();
}
?>