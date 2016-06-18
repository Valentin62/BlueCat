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
include_once "CORE/Classes/interface.php"; // Interface Engine
define("THEME", $CORE->info('theme')); // L'on définis le TITRE avec une constante

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
    $requested_page = "index";
}else{
    $requested_page = $_GET['view_page'];
}

// Selection de la page actuelle
$req_select_current_page = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "pages WHERE slug LIKE :slug");
$req_select_current_page->execute(array(':slug' => $requested_page));
$select_current_page = $req_select_current_page->fetch();

if($req_select_current_page->rowCount() != 0){

    $req_select_all_pages = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "pages WHERE flag_actif LIKE 'TRUE' AND menu LIKE 'TRUE'");
    $req_select_all_pages->execute();
    $all_pages = $req_select_all_pages->fetchAll();

    $row_count = 0;

    foreach ($all_pages as $pages) {

        $row = new tpl_engine("menu/menu_row.tpl");
        $menuTemplates = array();

        foreach ($pages as $test) {
            $row->set_var("menu-title", $pages['nom']);

            // Si c'est l'accueil
            if($select_current_page['nom'] == $pages['nom']){
                $row->set_var("menu-url", "#");
            }elseif($pages['slug'] == "index"){
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { //HTTPS
                    $row->set_var("menu-url", "https://".$_SERVER['HTTP_HOST']);
                }else{ //HTTP
                    $row->set_var("menu-url", "http://".$_SERVER['HTTP_HOST']);
                }
            }else{
                $row->set_var("menu-url", "page/" . $pages['slug']);
            }

        }

        $menuTemplates[] = $row;
        if($row_count == 0){ $menu = tpl_engine::merge($menuTemplates); }else{ $menu .= tpl_engine::merge($menuTemplates);}
        $row_count++;

    }

    $display_menu = new tpl_engine("menu/menu.tpl"); // Initialisation de l'interface
    $display_menu->set_var("menu", $menu);
    $display_menu->set_var("sitename", $CORE->info('titre'));







    $layout = new tpl_engine("layout.tpl"); // Initialisation de l'interface

    if($CORE->info('index_engine') == "TRUE"){

        $req_select_SEO_information = $bdd->prepare('SELECT * FROM ' . $SQL_prefixe . 'index_engine');
        $req_select_SEO_information->execute();
        $BC_SEO = $req_select_SEO_information->fetch();

        $layout->set_var("meta", "

        <!-- BlueCat SEO -->
        <meta name=\"description\" content=\"".$BC_SEO['description']."\" />
        <meta name=\"keywords\" content=\"".$BC_SEO['mots_clefs']."\" />
        ".$BC_SEO['meta_supp']."

        ");

    }

    $layout->set_var("title", $select_current_page['nom']);
    $layout->set_var("sitelanguage", $CORE->info('langue_site'));
    $layout->set_var("CMSversion", $CORE->info('CMS_version'));

    if(empty($_GET['view_page'])){
        $layout->set_var("root", "Themes/".THEME."/");
    }else{
        $layout->set_var("root", "../Themes/".THEME."/");
    }

    $layout->set_var("sitename", $CORE->info('titre'));
    $layout->set_var("content", $display_menu->output());

    echo $layout->output(); // Render final de la PAGE

}else{

    // Page introuvable
    header('Location: /core/Errors/404.php');

}

/////////////////////////////////
/// Executions des scriptes si il y en a
/////////////////////////////////

$req_select_script = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "modules WHERE flag_actif LIKE 'TRUE' AND script LIKE 'TRUE'");
$req_select_script->execute();
while($add_script = $req_select_script->fetch()) {

    if (file_exists('Scripts/' . $add_script['nom'] . '.php')) {
        include_once 'Scripts/' . $add_script['nom'] . '.php';
    } else {
        $CORE->report_error('Impossible de charger le script '.$add_script['nom'].': Fichier introuvable (404) !', '');
    }

}

