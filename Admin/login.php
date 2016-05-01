<!DOCTYPE html>
<html lang="fr" xmlns:http="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Admin - Login">
    <meta name="author" content="Pierre TIELEMANS">
    <link rel="shortcut icon" href="img/favicon.ico">

    <title>Login - BlueCat</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <?php

    ////////////////////
    /// Gestion des Skins
    ////////////////////

    include('../CORE/core.php'); // Include du CORE
    $CORE = new CORE; // Initialisation du Builder

    echo "<link href='Skins/".$CORE->info('skin_panel').".css' rel='stylesheet'>";

    ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->

</head>

  <body class="login-body">

    <div class="container">
        <form class="form-signin" method="POST" action="">
            <h2 class="form-signin-heading">Connexion au Panel</h2>
            <div class="login-wrap">

    <?php

    if(!empty($_POST['pass']) && !empty($_POST['email'])){

        if($CORE->info('algorithme') == "sha1"){
            $formpass = sha1($_POST['pass']);
        }elseif($CORE->info('algorithme') == "md5"){
            $formpass = md5($_POST['pass']);
        }else{
            $CORE->report_error('Algorithme erroné ! pas de MD5 ou SHA1 sélectionné');
        }

        $formemail = $_POST['email'];

        // On prépare la requête
        $requete = $bdd->prepare('SELECT * FROM ' . $SQL_prefixe . 'utilisateurs WHERE pass LIKE :pass AND email LIKE :email');

        $requete->bindValue(':pass', $formpass, PDO::PARAM_STR);
        $requete->bindValue(':email', $formemail, PDO::PARAM_STR);

        //On exécute la requête
        $requete->execute();

        $requ_fetch = $requete->Fetch();

        // On récupère le résultat
        if ($requ_fetch['flag_actif'] == "TRUE")
        {
            $session =  md5(rand());

            $updatemembre = $bdd->prepare('UPDATE ' . $SQL_prefixe . 'utilisateurs SET token = :token WHERE email = :email');
            $updatemembre->execute(array('token' => $session, 'email' => $formemail));

            $_SESSION['email'] = $formemail;
            $_SESSION['sessid'] = $session;

            if(!empty($_POST['stay'])){

                setcookie("token", $session, time()+2678400, "/"); // 31 Jours

            }else{

                setcookie("token", $session, time()+172800, "/"); // 2 Jours

            }

            setcookie("user", $formemail, time()+172800, "/"); // 2 Jours)

            $CORE->Alert('<strong>YEAHHH !</strong> Redirection au panel...', 'success');
            header( "refresh:1;url=dashboard.php" );


        }else{

            if($requ_fetch['flag_actif'] == "FALSE"){
                $CORE->Alert('<strong>Attention !</strong> Votre compte est désactivé !', 'warning');
            }else{
                $CORE->Alert("<strong>Raté !</strong> L'utilisateur ou le mot de passe est erroné !", 'danger');
            }

        }

    }

    ?>

            <input type="text" name="email" class="form-control" placeholder="Adresse Email" autofocus>
            <input type="password" name="pass" class="form-control" placeholder="Mot de passe">
            <label class="checkbox">
                <input type="checkbox" name="stay"> Se souvenir de moi
                <span class="pull-right">
                    <a data-toggle="modal" href="#myModal"> Mot de passe oublié?</a>

                </span>
            </label>
            <button class="btn btn-lg btn-login btn-block" type="submit">Connexion !</button>

        </div>

          <!-- Modal -->
          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h4 class="modal-title">Mot de passe oublié ?</h4>
                      </div>
                      <div class="modal-body">
                          <p>Si vous êtes l'administrateur principal de ce site, rendez vous dans votre FTP à CORE>Vars>recovery.php et suivez-y les instructions.</p>
                      </div>
                      <div class="modal-footer">
                          <button data-dismiss="modal" class="btn btn-success" type="button">Ok !</button>
                      </div>
                  </div>
              </div>
          </div>
          <!-- modal -->

      </form>

    </div>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>

  </body>
</html>
