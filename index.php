<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

/////////////
/// Includes/Définition des variables
/////////////

include('CORE/core.php'); // Include du CORE
$CORE = new CORE; // Initialisation du CORE
define("THEME", $CORE->info('theme')); // L'on définis le TITRE avec une constante
define('THEMEPATH', "/Themes/".THEME."/");

$get_json_manifest = json_decode(file_get_contents("Themes/".THEME."/manifest.json"), true);

//////////////////////////
/// Initialisation de CK
/////////////////////////
if(CORE::Connecte() == TRUE) {
    include('CORE/Classes/contenu.php');
    $CONTENU = new Contenu;
}

//////////////////////////
/// Système d'affichage des page(s)
//////////////////////////

if(empty($_GET['view_page'])){
    $page_request = "index";
    $fichier_demande = 'Themes/'.THEME.'/index.php';
    include($fichier_demande);
}else{
    $fichier_demande = 'Themes/'.THEME.'/'.$_GET['view_page'].'.php';
    if (file_exists($fichier_demande)) {
        include($fichier_demande); // Si le fichier demandé existe l'on l'affche
    }else{
        include('CORE/Errors/404/index.html'); // Le fichier demandé n'existe pas, donc 404
    }
}


