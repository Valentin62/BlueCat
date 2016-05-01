<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

echo "Redirection en cours...";

include('../CORE/Vars/SQL.php');

if(!empty($_COOKIE['token'])){

    $id_cookie = $_COOKIE['token'];

}else{

    header( "refresh:0;url=login.php" );

}

session_start();
$sess_mail = $_SESSION['email'];

$req_select_user = $bdd->prepare('SELECT * FROM ' . $SQL_prefixe . 'utilisateurs WHERE token LIKE :token AND email LIKE :sess');
$req_select_user->execute(array(':token' => $_COOKIE['token'], ':sess' => $sess_mail));
$select_user = $req_select_user->rowCount();
if($select_user == 1 && $sess_mail == $_COOKIE['user']){
    header( "refresh:0;url=dashboard.php" );
}else{
    header( "refresh:0;url=login.php" );
}

?>
