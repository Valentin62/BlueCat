<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

/////////////
/// Includes/Require
/////////////
include('Vars/SQL.php'); // Ajout de la connexion SQL
session_start();

if(!empty($_SESSION)){
    $sess_mail = $_SESSION['email'];
}

class CORE {

    /////////////////////////
    /// Insert d'un évènement d'erreur
    /////////////////////////
    public function report_error($error, $debug){

        global $CORE;

        if($CORE->info('debug') == "TRUE" OR $debug == "TRUE"){ // Affichage de l'erreur chez le client

            echo "<script>console.log('ERREUR: ".$error."');</script>";

        }else{ // Affichage de l'erreur sur le serveur

            $file_logs = '/Logs/logs.txt';

            if (file_exists($file_logs)) {
                $current_file_logs = file_get_contents(dirname(__FILE__) . $file_logs);
                $current_file_logs .= "[" . date('Y-m-d H:i:s') . "] - ERREUR: " . $error . "\n";
                file_put_contents(dirname(__FILE__) . $file_logs, $current_file_logs);
            } else {
                fopen(dirname(__FILE__) . $file_logs, 'w+');
                $current_file_logs = "[" . date('Y-m-d H:i:s') . "] - INFO: Création du fichier de logs \n";
                $current_file_logs .= "[" . date('Y-m-d H:i:s') . "] - ERREUR: " . $error . "\n";
                file_put_contents(dirname(__FILE__) . $file_logs, $current_file_logs);
            }

        }

    }

    ////////////////////////
    /// Récuperation des infos générales
    /// VARCHAR
    ////////////////////////
    public function info($get_info){

        global $bdd;
        global $SQL_prefixe;

        // Définition des IDs de recherche
        if($get_info == "CMS_version") {
            $findwithid = 3;
        }elseif($get_info == "CMS_nom_version"){
            $findwithid = 4;
        }elseif($get_info == "effets"){
            $findwithid = 6;
        }elseif($get_info == "titre"){
            $findwithid = 8;
        }elseif($get_info == "index_engine"){
            $findwithid = 11;
        }elseif($get_info == "theme"){
            $findwithid = 7;
        }elseif($get_info == "skin_panel"){
            $findwithid = 12;
        }elseif($get_info == "algorithme"){
            $findwithid = 13;
        }elseif($get_info == "debug"){
            $findwithid = 2;
        }elseif($get_info == "max_try"){
            $findwithid = 14;
        }elseif($get_info == "langue_site"){
            $findwithid = 15;
        }else{
            $this->report_error('Sélection d\'un paramètres introuvable ( '.$get_info.' ) !', '');
        }
        
        $req_select_params = $bdd->prepare('SELECT valeur FROM ' . $SQL_prefixe . 'parametres WHERE id LIKE :id');
        $req_select_params->execute(array(':id' => $findwithid));
        $select_params = $req_select_params->fetch();
        return $select_params['valeur'];

    }

    //////////////////////////////
    /// Vérifie si la licence est bonne
    /// BOOLEAN
    //////////////////////////////
    public static function verify_licence(){

        global $bdd;
        global $SQL_prefixe;

        // Sélection de la licence en DB
        $req_select_licence = $bdd->prepare('SELECT valeur FROM ' . $SQL_prefixe . 'parametres WHERE id LIKE 9');
        $req_select_licence->execute();
        $select_licence = $req_select_licence->fetch();
        $now_licence = $select_licence['valeur'];

        // Sélection du dernier check
        $req_select_licence_lastcheck = $bdd->prepare('SELECT valeur FROM ' . $SQL_prefixe . 'parametres WHERE id LIKE 10');
        $req_select_licence_lastcheck->execute();
        $select_licence_lastcheck = $req_select_licence_lastcheck->fetch();
        $licence_lastcheck_u = $select_licence_lastcheck['valeur'];

        if(empty($now_licence)){
            return "FALSE"; // Si la licence est non définie l'on retourne un "FALSE"
        }else{
            //if($licence_lastcheck_u <= time('U')){
                $request_licence_verification = file_get_contents('http://apis.pierre-tielemans.be/CMS/?licence='.$now_licence.'&domaine='.$_SERVER['SERVER_NAME']);
                if($request_licence_verification == "TRUE") {
                    return "TRUE"; // Si l'API retourne "TRUE" et l'on le confirme
                    $update_lastcheck = $bdd->prepare("UPDATE ".$SQL_prefixe."parametres SET valeur=:valeur WHERE id=10");
                    $update_lastcheck->execute(array(':valeur' => time('U')+25200)); // Ajout de 7 jours
                }else{
                    // La licence entrée est incorrecte on retourne "FALSE"
                    return "FALSE";
                }
            //}
        }

    }

    public static function Canido($that){ // TODO: système de droits

        global $bdd;
        global $SQL_prefixe;

    }

    ////////////////////////////
    /// Vérifie si l'utilisateur est connecté
    /// BOOLEAN
    ////////////////////////////
    public static function Connecte(){

        global $bdd;
        global $SQL_prefixe;
        global $sess_mail;

        if(isset($_COOKIE['token'])){
            $req_select_user = $bdd->prepare('SELECT * FROM ' . $SQL_prefixe . 'utilisateurs WHERE token LIKE :token');
            $req_select_user->execute(array(':token' => $_COOKIE['token']));
            $select_user = $req_select_user->rowCount();
            if ($select_user == 1 && $sess_mail == $_COOKIE['user']) {
                // Si l'utilisateur est connecté
                return "TRUE";
            } else {
                // Si il n'est pas connecté
                return "FALSE";
            }
        }else{
            // Si il n'est pas connecté
            return "FALSE";
        }

    }

    //////////////////////
    /// Crée une alerte BTS
    /// VARCHAR
    //////////////////////
    public function Alert($message, $type) {

        echo "
		<div class='alert alert-dismissible alert-".$type."'>
		  <button type='button' class='close' data-dismiss='alert'>×</button>
		  ".$message."
		</div> " ;

    }

    /////////////////////////
    /// Banni une IP du panel ou vérifie si elle l'est
    /// BOOLEAN
    /////////////////////////
    public static function banIP($ipban, $totime, $raison){

        global $bdd;
        global $SQL_prefixe;

        $req_select_ban = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "ban WHERE ip LIKE :ip");
        $req_select_ban->execute(array(':ip' => $ipban));

        if(!empty($totime) && $req_select_ban->rowCount() == 0) {

            $add_ban = $bdd->prepare('INSERT INTO ' . $SQL_prefixe . 'ban (ip, totime, raison) VALUES (:ip, :totime, :raison)');
            $add_ban->execute(array(':ip' => $ipban, ':totime' => $totime, ':raison' => $raison));

        }else{

            if ($req_select_ban->rowCount() == 1) {
                $bantime = $req_select_ban->fetch();

                if(time('U') >= $bantime['totime']){
                    return "FALSE";
                    $req_unban = $bdd->prepare("DELETE FROM " . $SQL_prefixe . "ban WHERE ip LIKE :ip");
                    $req_unban->execute(array(':ip' => $ipban));
                }else{
                    return "TRUE";
                }

            }else{
                return "FALSE";
            }

        }

    }

    /////////////////////////
    /// Regarder si une MAJ est disponible
    /// BOOLEAN
    /////////////////////////
    public function checkupdate(){

        $request_lastversion = file_get_contents('http://apis.pierre-tielemans.be/CMS/?lastversion=1&domaine='.$_SERVER['SERVER_NAME']);

        $arrayversion = json_decode($request_lastversion, 1);

        if($this->info('CMS_version') < $arrayversion['version']){
            return "$request_lastversion";
        }else{
            return "FALSE";
        }

    }

    /////////////////////////
    /// Installer une MAJ
    /// BOOLEAN
    /////////////////////////
    public function installupdate()
    {

        global $bdd;
        global $SQL_prefixe;

        $download_version = json_decode($this->checkupdate(), 1);

        if (self::verify_licence() == "TRUE" && $download_version['version'] > $this->info('CMS_version')){

            $file = $download_version['version'] . '.zip';

            echo "\n
                 ############################################# \n
                 # Installateur de mise à jour natif BlueCat # \n
                 ############################################# \n
                 \n
                 Version à installer: ".$download_version['version']." ( ".$download_version['version_nom']." )
                 \n
                 ";

            if (!is_file('TEMP/BLUECAT-' . $download_version['version'] . '.zip')) {
                echo 'Téléchargement de la MAJ à partir de GitHub...';
                $newUpdate = file_get_contents("http://dev.pierre-tielemans.be/BLUECAT-0.0.1.zip"); // https://github.com/pierretielemans/BlueCat/archive/".$download_version['version'].".zip
                if (!is_dir('TEMP/')) mkdir('TEMP/');
                $dlHandler = fopen('TEMP/BLUECAT-' . $download_version['version'] . '.zip', 'w');
                echo $dlHandler;
                if (!fwrite($dlHandler, $newUpdate)) {
                    echo 'Impossible de sauvgarder l\'archive: ' . $_ENV['site']['files']['includes-dir'];
                    exit();
                }
                fclose($dlHandler);
                echo 'Mise à jours téléchargée et sauvgardée';
            } else echo 'Mise à jours déja téléchargée.';

            $zipHandle = zip_open('TEMP/BLUECAT-' . $download_version['version'] . '.zip');

            while ($aF = zip_read($zipHandle)) {
                $thisFileName = zip_entry_name($aF);
                $thisFileDir = dirname($thisFileName);

                //Continue if its not a file
                if (substr($thisFileName, -1, 1) == '/') continue;


                //Make the directory if we need to...
                if (!is_dir('/' . $thisFileDir)) {
                    mkdir('/' . $thisFileDir);
                    echo "Répertoire créé $thisFileDir";
                }

                //Overwrite the file
                if (!is_dir('/' . $thisFileName)) {
                    echo "Création d'uu répertoire";
                    $contents = zip_entry_read($aF, zip_entry_filesize($aF));
                    $contents = str_replace("\r\n", "\n", $contents);
                    $updateThis = '';

                    //If we need to run commands, then do it.
                    if ($thisFileName == 'upgrade.php') {
                        $upgradeExec = fopen('upgrade.php', 'w+');
                        fwrite($upgradeExec, $contents);
                        fclose($upgradeExec);
                        include('upgrade.php');
                        unlink('upgrade.php');
                        echo 'Fichier (' . $thisFileName . ') Éxecuté';
                    } else {
                        $updateThis = fopen($thisFileName, 'w+');
                        fwrite($updateThis, $contents);
                        fclose($updateThis);
                        unset($contents);
                        echo 'Fichier (' . $thisFileName . ') mis à jour/créé';
                    }
                }
            }

            if (unlink('TEMP/BLUECAT-' . $download_version['version'] . '.zip')) {
                echo 'Archive de mise à jour supprimée';
            } else {
                echo 'Impossible de supprimer l\'archive de mise à jour';
            }

            echo 'Mise à jour de la base de donnée';

            $update_new_version = $bdd->prepare("UPDATE ".$SQL_prefixe."parametres SET valeur=:valeur WHERE id=3");
            $update_new_version->execute(array(':valeur' => $download_version['version']));
            $update_new_version_nom = $bdd->prepare("UPDATE ".$SQL_prefixe."parametres SET valeur=:valeur WHERE id=4");
            $update_new_version_nom->execute(array(':valeur' => $download_version['version_nom']));

            echo "\n
                 ############################################# \n
                 # Installateur de mise à jour natif BlueCat # \n
                 ############################################# \n
                 \n
                 Mise à jour terminée !
                 \n
                 Nouvelle version installée: ".$download_version['version']." ( ".$download_version['version_nom']." )
                 \n
                 ";

            return "TRUE";

            //Envois des logs
            //$this->report_error($logfile, '');

        }else{ // La licence est invalide

            return "FALSE";

            if(self::verify_licence() !== "TRUE"){
                $this->report_error('Votre CMS doit avoir une licence valide pour être mis à jour !', '');
            }else{
                $this->report_error('Votre CMS est mis à jour !', '');
            }


        }

    }

}

$CORE = new CORE;
CORE::verify_licence();

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// Scriptes à laisser en fin de fichier absolument !!!! /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

include('builder.php'); // Include du Builder
$BUILDER = new Build; // Initialisation du Builder

