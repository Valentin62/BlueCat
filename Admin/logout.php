<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

echo "Déconnexion en cours...";

session_start();
$sess_mail = $_SESSION['email'];
session_unset();
session_destroy();

include('../CORE/Vars/SQL.php');

$updatemembre = $bdd->prepare("UPDATE ' . $SQL_prefixe . 'utilisateurs SET token = :token WHERE email = :email");
$updatemembre->execute(array(':token' => "100", ':email' => $sess_mail));

if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

header( "refresh:0;url=login.php" );

?>
