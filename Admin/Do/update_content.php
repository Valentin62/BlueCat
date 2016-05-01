<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

/////////////
/// Includes/Require
/////////////
include('../../CORE/core.php'); // Include du CORE
$CORE = new CORE; // Initialisation du CORE

if(CORE::Connecte() == TRUE) {
    $update_contenu = $bdd->prepare('UPDATE ' . $SQL_prefixe . 'contenu SET valeur = :valeur WHERE nom = :nom AND theme LIKE :theme');
    $update_contenu->execute(array(':valeur' => $_POST['datavalue'], ':nom' => $_POST['dataname'], ':theme' => $CORE->info(theme)));
}else{
    $CORE->Alert("<strong>Accès refusé !</strong> Vous n'avez pas accès à cette page !", 'danger');
    header("refresh:2;url=../index.php");
}

?>
