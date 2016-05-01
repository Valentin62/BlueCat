<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

///////////////////////
/// Données de connexion SQL
//////////////////////

$SQL_ip = "localhost";
$SQL_user = "root";
$SQL_pass = "4bFHK8170a";
$SQL_dbname = "CMS";
$SQL_prefixe = "CMS_";

// Connexion à la BDD
$bdd = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $SQL_ip, $SQL_dbname), $SQL_user, $SQL_pass);

?>
