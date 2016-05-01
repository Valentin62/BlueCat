<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

//////////////////
/// Includes
//////////////////
include('../../CORE/core.php'); // Include du CORE
$CORE = new CORE; // Initialisation du CORE

if(isset($_GET['stop_modify'])){
    setcookie("modif", "FALSE", time() - 3600, "/");
    header("refresh:0;url=../index.php");
}else{
    if (CORE::Connecte() == TRUE) {
        setcookie("modif", "TRUE", time() + 3600, "/");
        header("refresh:0;url=../../index.php");
    } else {
        $CORE->Alert("<strong>Accès refusé !</strong> Vous n'avez pas accès à cette page !", 'danger');
        header("refresh:2;url=../index.php");
    }
}

?>
