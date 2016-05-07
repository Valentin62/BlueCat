<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

/////////////
/// Includes/Require
/////////////

define("TITRE", $CORE->info('titre')); // L'on définis le TITRE avec une constante

class Build {

    /////////////////////////
    /// Création du header
    ////////////////////////
    public static function Header($titre) {

        global $bdd;
        global $SQL_prefixe;

        // Si le CMS n'as pas de licence l'on le rappelle dans le CS
        if(CORE::verify_licence() == "FALSE"){
            echo "
            <!--

            //////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Ce CMS n'as pas de licence ou celle-ci est invalide, activez votre CMS pour enlever ce message. //
            /// Vous n'avez pas de clef d'activation ? Rendez-vous sur: www.leCMS.com/activer                   //
            /// Vous avez une clef valide ? Connectez-vous au panel et puis: Paramètres>Activer le CMS          //
            //////////////////////////////////////////////////////////////////////////////////////////////////////

            Copyright ".date("Y").", tous droits réservés par Pierre TIELEMANS (www.pierre-tielemans.be)

            -->
            ";
        }

        // L'on définis le titre de la page
        if(!empty($titre)){
            echo "<title>".TITRE." - ".$titre."</title>";
        }else{
            echo "<title>".TITRE." - SANS TITRE</title>"; // Si le titre n'est pas spécifié, l'on le crée
        }

        // Balises METAs
        global $CORE;
        if($CORE->info('index_engine') == "TRUE"){
            $req_select_index_params = $bdd->prepare('SELECT * FROM ' . $SQL_prefixe . 'index_engine WHERE id LIKE 1');
            $req_select_index_params->execute();
            $select_index_params = $req_select_index_params->fetch();
            echo "
                <meta charset='utf-8' />
                <meta name='Description' content='".$select_index_params['description']."' />
                <meta name='Keywords' content='".$select_index_params['mots_clefs']."'/>
                <meta name='robots' content='index, follow, archive' />
                <meta name='robots' content='all' />
                <link rel='stylesheet' href='/CORE/Dep/style.css'>
                ".$select_index_params['meta_supp']."
            ";

            if(isset($_COOKIE['modif']) && CORE::Connecte() == "TRUE") {
                echo "
                <script   src=\"https://code.jquery.com/jquery-2.2.3.min.js\"   integrity=\"sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo=\"   crossorigin=\"anonymous\"></script>
                <script src='CORE/Plugins/ckeditor/ckeditor.js' ></script>
                ";
            }

        }else{
            echo "
                <meta charset='utf-8' />
            ";
        }

    }

    /////////////////////////
    /// Création du Body
    ////////////////////////
    public static function Body() {



    }

    /////////////////////////////////////
    /// Création de l'outil d'aide à l'inclusion des fichiers
    /////////////////////////////////////
    public static function addFile($file_to_include){
        $arraydebugb = debug_backtrace();
        $finalarray = explode("/", $arraydebugb[0]['file']);
        include dirname(__DIR__)."/".$finalarray[4]."/".$finalarray[5]."/".$file_to_include;
    }

    //////////////////////////
    /// Création d'un widget
    //////////////////////////
    public function Widget_area($nom){

        global $bdd;
        global $SQL_prefixe;

        // Sélection des informations relatives à la zone de module
        $req_select_module_wp = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "module_area WHERE nom LIKE :nom AND flag_actif LIKE 'TRUE'");
        $req_select_module_wp->execute(array('nom' => $nom));
        $select_module_wp = $req_select_module_wp->fetch();
        $module = $select_module_wp['widget'];
        $color = $select_module_wp['color'];

        if(empty($module)){

            echo "

            <div class=\"panel\" style=\"background-color: ".$color."\">
              <header class=\"panel-heading\">
                  <li class=\"dropdown\" style=\"list-style-type: none;\">
                      <a class=\"dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"icon-cogs\"><i class=\"icon-arrow-down\"></i></i> MODULE - <strong>NON ASSIGNÉ</strong></a>
                      <ul class=\"dropdown-menu col-sm-10\">
                          <li>
                              <div class=\"col-sm-12\">
                                  <div class=\"input-group col-sm-12\">
                                      <center><p>Changez l'assignation de ce widget</p></center>
                                      <form style=\"padding: 15px; padding-bottom: 0px;\" method=\"post\" action=\"Do/update_widget.php\">
                                          <select name='widget' class=\"form-control \">";

                                            $req_select_modules = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "modules WHERE flag_actif LIKE 'TRUE'");
                                            $req_select_modules->execute();
                                            while($select_modules = $req_select_modules->fetch()) {

                                                $select_m_nom = $select_modules['nom'];
                                                $select_m_version = $select_modules['version'];
                                                $select_u_widget = $module; // TODO: simplifier variables

                                                if ($select_u_widget == $select_m_nom) {
                                                    echo "<option value='" . $select_m_nom . "' selected><bold>" . $select_m_nom . " [ " . $select_m_version . " ]</bold></option>";
                                                }else{
                                                    echo "<option value='".$select_m_nom."'>".$select_m_nom." [ ".$select_m_version." ]</option>";
                                                }

                                            }

                                            echo "
                                          </select>
                                          <br/> <center>-</center>
                                          <input type=\"text\" name='color' class=\"colorpicker-default form-control\" value=\"".$color."\">
                                          <input name='nom' value='".$nom."' hidden>
                                          <br/><br/>-
                                          <center><button type=\"submit\" class=\"btn btn-success\"><i class=\"icon-eye-open\"></i> Enrengistrer </button></center>
                                      </form>
                                  </div>
                              </div>
                          </li>
                      </ul>
                  </li>
              </header>
              <div class=\"panel-body\">
                <p><center><strong>Aucun module assigné !</strong><br/>Clic sur le titre et assignes en un.</center></p>
              </div>
            </div>
            <script> $(function() { $('.dropdown-toggle').dropdown(); $('.dropdown input, .dropdown label').click(function(e) { e.stopPropagation(); }); }); </script>

        ";

        }else{ // Si un module est associé

            echo "

            <div class=\"panel\" style=\"background-color: ".$color."\">
              <header class=\"panel-heading\">
                  <li class=\"dropdown\" style=\"list-style-type: none;\">
                      <a class=\"dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"icon-cogs\"><i class=\"icon-arrow-down\"></i></i> MODULE - ".$module."</a>
                      <ul class=\"dropdown-menu col-sm-10\">
                          <li>
                              <div class=\"col-sm-12\">
                                  <div class=\"input-group col-sm-12\">
                                      <center><p>Changez l'assignation de ce widget</p></center>
                                      <form style=\"padding: 15px; padding-bottom: 0px;\" method=\"post\" action=\"Do/update_widget.php\">
                                          <select name='widget' class=\"form-control \">";

                                            $req_select_modules = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "modules WHERE flag_actif LIKE 'TRUE'");
                                            $req_select_modules->execute();
                                            while($select_modules = $req_select_modules->fetch()) {

                                                $req_select_modules_used = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "module_area");
                                                $req_select_modules_used->execute();
                                                $select_modules_used = $req_select_modules_used->fetch();

                                                $select_m_nom = $select_modules['nom'];
                                                $select_m_version = $select_modules['version'];
                                                $select_u_widget = $select_modules_used['widget'];

                                                if ($select_u_widget == $select_m_nom) {
                                                    echo "<option value='" . $select_m_nom . "' selected><bold>" . $select_m_nom . " [ " . $select_m_version . " ]</bold></option>";
                                                }else{
                                                    echo "<option value='".$select_m_nom."'>".$select_m_nom." [ ".$select_m_version." ]</option>";
                                                }

                                            }

                                            echo "
                                          </select>
                                          <br/> <center>-</center>
                                          <input type=\"text\" name='color' class=\"colorpicker-default form-control\" value=\"".$color."\">
                                          <input name='nom' value='".$nom."' hidden>
                                          <br/><br/>
                                          <center><button type=\"submit\" class=\"btn btn-success\"><i class=\"icon-eye-open\"></i> Enrengistrer </button></center>
                                      </form>
                                  </div>
                              </div>
                          </li>
                      </ul>
                  </li>
              </header>
              <div class=\"panel-body\">
              "; if (file_exists('../Modules/'.$module.'/index.php')) { include('../Modules/'.$module.'/index.php'); }else{ echo "<center><p style='color: red;'><strong>ERREUR !</strong><br/>Impossible de charger le module: l'index est introuvable. <br/>Contactez le développeur de ce module.</p></center>"; } echo "
              </div>
            </div>
            <script> $(function() { $('.dropdown-toggle').dropdown(); $('.dropdown input, .dropdown label').click(function(e) { e.stopPropagation(); }); }); </script>

        ";

        }

    }

}
