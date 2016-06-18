<?php

///////////////////////////////////////////////////////
/// Script natif de statistiques
/// Développé par Pierre TIELEMANS
/// www.pierre-tielemans.be
///////////////////////////////////////////////////////


/////////////////////
/// Récuperation des infos de l'utilisateur
/////////////////////

// Browser
if(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),strtolower("MSIE"))){$browser="ie";}
else if(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),strtolower("Presto"))){$browser="opera";}
else if(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),strtolower("CHROME"))){$browser="chrome";}
else if(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),strtolower("SAFARI"))){$browser="safari";}
else if(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),strtolower("FIREFOX"))){$browser="firefox";}
else {$browser="Autre";}

// IP + Géolocalisation
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
if($query && $query['status'] == 'success') {
    $pays = $query['country'];
} else {
    $pays = "/";
}

$CORE->report_error($browser,'DEBUG');

// Page demandée
if ($_SERVER['REQUEST_URI'] == "/"){
    $page_req = "/index";
}else{
    $page_req = $_SERVER['REQUEST_URI'];
}

// Vérification si c'est un bot connu ou un humain
if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot")){
    $bot = "Google";
}elseif(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "Bingbot")){
    $bot = "Bing";
}else{
    $bot = "FALSE";
}

$req_select_modules = $bdd->prepare("SELECT * FROM " . $SQL_prefixe . "stats WHERE ip LIKE :ip");
$req_select_modules->execute(array(':ip' => $ip));
if ($req_select_modules->rowCount() <= 3 && $bot == "FALSE"){ // Si l'utilisateur n'a pas participé plus de 3X aux stats, on l'ajoute

    $add_stat = $bdd->prepare('INSERT INTO ' . $SQL_prefixe . 'stats (browser_name, ip, fichier, hheure, ddate, pays, bot) VALUES (:browser_name, :ip, :fichier, :hheure, :ddate, :pays, :bot)');
    $add_stat->execute(array(':browser_name' => $browser, ':ip' => $ip, ':fichier' => $page_req, ':hheure' => date("H:i:s"), ':ddate' => date("d.m.y"), ':pays' => $pays, ':bot' => $_SERVER['HTTP_USER_AGENT']));

}

?>
