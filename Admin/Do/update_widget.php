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
    $update_contenu = $bdd->prepare('UPDATE ' . $SQL_prefixe . 'module_area SET widget = :widget, color = :color WHERE nom LIKE :nom');
    $update_contenu->execute(array(':widget' => $_POST['widget'], ':color' => $_POST['color'], ':nom' => $_POST['nom']));
    header("refresh:0;url=../index.php");
}else{
    $CORE->Alert("<strong>Accès refusé !</strong> Vous n'avez pas accès à cette page !", 'danger');
    header("refresh:2;url=../index.php");
}

?>
