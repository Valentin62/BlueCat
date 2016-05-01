<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

if(CORE::verify_licence() == "FALSE"){
    $color_footer = "red";
    $no_licence_msg = "- <BLINK><strong>Licence non valide </strong></BLINK>";
}else{
    $color_footer = "";
    $no_licence_msg = "";
}

?>

<!--footer start-->
<footer class="site-footer" style="background-color: <?php echo $color_footer; ?>;">
    <div class="text-center">
        <?php echo date("Y"); ?> &copy; <strong>Bluecat CMS</strong> version <?php echo $CORE->info('CMS_version'); ?> [ <?php echo $CORE->info('CMS_nom_version'); ?> ] <?php echo $no_licence_msg; ?> - développé par <strong><a title="Site du développeur" style="color: white;" href="http://www.pierre-tielemans.be/" target="_blank">Pierre TIELEMANS</a></strong>.
        <a href="#" class="go-top">
            <i class="icon-angle-up"></i>
        </a>
    </div>
</footer>
<!--footer end-->
