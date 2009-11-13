<?php /* -*- coding: utf-8 -*- */

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