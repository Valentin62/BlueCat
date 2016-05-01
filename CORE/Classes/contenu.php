<?php
/**
 * Copyright (c) 2016. Tous droits réservés.
 * Pierre TIELEMANS (www.pierre-tielemans.be) - contact[at]pierre-tielemans.be
 */

/////////////
/// Includes/Require
/////////////

class Contenu{

    public static function Texte($nom){ // TODO: CK Fonctionne pas !

        global $bdd;
        global $SQL_prefixe;

        $req_select_texte = $bdd->prepare('SELECT * FROM ' . $SQL_prefixe . 'contenu WHERE nom LIKE :nom');
        $req_select_texte->execute(array(':nom' => $nom));
        $select_texte = $req_select_texte->fetch();

        if(isset($_COOKIE['modif']) && CORE::Connecte() == "TRUE") {

            echo "<a href='/Admin/Do/modify_content.php?stop_modify' class='leave_edit'>Quitter l'édition</a>";

            if(empty($select_texte['valeur'])){
                $select_texte['valeur'] = "<p style='color: red;'>Contenu vide</p>";
            }

            echo "<div id='".$nom."' contenteditable='true'>".$select_texte['valeur']."</div>";

            echo "<script>new CKEDITOR(); CKEDITOR.disableAutoInline = true; CKEDITOR.inline('".$nom."'); CKEDITOR.appendTo('".$nom."');</script>";

        }else{

            echo $select_texte['valeur'];

        }

    }

}

?>