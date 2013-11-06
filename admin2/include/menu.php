<?php
  // On est ici dans la page du menu qui va être inclus sur toutes
  // les pages de la partie administration du site
  if (strpos($_SERVER["SCRIPT_NAME"], "_dev"))
  {
    print "<div style='color:red; font-weight:bold; text-align:center; margin:auto; font-size:1.5em'>Version de développement</div>";
  }
  print "<div id='menu'>\n";
  print "<ul>\n";
  elt_menu("planning.php", "Planning", "calendrier");
  if ( ($_SESSION["privilege"] == "admin") || ($_SESSION["privilege"] == "autogestion") )
  {
    elt_menu("perso.php", "Personnel", "personnes");
  }


//  elt_menu("conges.php", "Congés", "vacances");
  elt_menu("stats.php", "Statistiques", "stat", 1);
  elt_menu("index.php?action=logout", "Déconnexion", "exit");
  print "</ul>\n";
  # On va ensuite afficher le titre de la page
  print "</div>";
 

  function elt_menu($lien, $lib, $code, $priv = 0)
  {
    $url_courante = basename($_SERVER['SCRIPT_NAME']);
  
    if ( ($priv == 1) && ($_SESSION["privilege"] != "admin") )
    {
      return;
    }
  
    if (strpos($url_courante, $lien) === 0)
    {
      // Page courante
      print "<li><a href='$lien' id='$code' class='lien_courant'>$lib</a></li>\n";
    }
    elseif ( ($lien == "perso.php") && (substr($url_courante, 0, 3) == "pe_" ) )
    {
      print "<li><a href='$lien' id='$code' class='lien_courant'>$lib</a></li>\n";
    }
    else
    {
      print "<li><a href='$lien' id='$code'>$lib</a></li>\n";
    }
  }
?>