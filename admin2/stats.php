<?php
  include("include/connect.php");
  include("include/utils.php");
  $code_page = "stats";
  include("include/haut.php");
  include("include/utils_stats.php");
?>
  <h1>Statistiques</h1>
<?php
  if (isset($_GET["type"]))
  {
    $type = $_GET["type"];
  }
  else
  {
    $type = "";
  }
  
  lien_menu_stats('Semaine', 'semaine', $type, 1);
  lien_menu_stats('Mois', 'mois', $type, 1);
  lien_menu_stats('Année', 'annee', $type, 1);
  lien_menu_stats('Intervalle de dates', 'periode', $type, 0);
  print "<div id='global_80pc'>";
  
  if (isset($_GET["annee"]))
  {
    $annee = $_GET["annee"];
  }
  else
  {
    $annee = "";
  }
  
  if (isset($_GET["mois"]))
  {
    $mois = $_GET["mois"];
  }
  else
  {
    $mois = "";
  }
  
  if (isset($_GET["semaine"]))
  {
    $semaine = $_GET["semaine"];
  }
  else
  {
    $semaine = "";
  }
  
  if (isset($_GET["jour_deb"]))
  {
    $jour_deb = $_GET["jour_deb"];
  }
  else
  {
    $jour_deb = "";
  }

  if (isset($_GET["jour_fin"]))
  {
    $jour_fin = $_GET["jour_fin"];
  }
  else
  {
    $jour_fin = "";
  }

  if (isset($_POST["intervalle_deb"]))
  {
    $intervalle_deb = $_POST["intervalle_deb"];
  }
  else
  {
    $intervalle_deb = "";
  }

  if (isset($_POST["intervalle_fin"]))
  {
    $intervalle_fin = $_POST["intervalle_fin"];  
  }
  else
  {
    $intervalle_fin = "";
  }
  
  print "<div id='selection_stats'>\n";
  if ($type == "annee")
  {
    // On va proposer la sélection des années
    affiche_annees($annee, $type);
  }
  elseif($type == "mois")
  {
    affiche_mois($annee, $mois, $type);
  }
  elseif($type == "semaine")
  {
    affiche_semaine($annee, $semaine, $type);
  }
  elseif($type == "periode")
  {
    // Deux cas différents selon que l'on a déjà saisi un intervalle ou pas
    print "<form method='POST'>\n";
    print '<label for="intervalle_deb">Du : </label>'."\n";
    print "<input type='text' id='intervalle_deb' name='intervalle_deb' size='10'";
    print "value='".$intervalle_deb."'";
    print "/>\n<br/>";
    print '<label for="intervalle_fin">au : </label>'."\n";
    print "<input type='text' id='intervalle_fin' name='intervalle_fin' size='10'";
    print "value='".$intervalle_fin."'";
    print "/><br/><br/>\n";
    print "<center><input type='submit' value='Valider'/></center>";
    print "</form>\n";

  }
  
  print "</div> <!-- #selection_stats -->\n";
  // On va afficher les statistiques
  if (
    ( ($type == "annee") && ($annee != "") ) ||
    ( ($type == "semaine") && ($semaine != "") ) ||
    ( ($type == "mois") && ($mois != "") ) ||
    ( ($type == "periode") && ( ($intervalle_deb != "") || ($intervalle_fin != "") ) )
  )
  {
    print "<div id='stats'>\n";
    
    # On affiche le groupe, on va profiter de cet endroit pour gérer des onglets et
    # faire en sorte que cela réponde à la demande d'alia
    # ATTENTION : Si mise à jour, penser à la faire aussi dans le fichier stats_pdf.php aussi
    $tab_groupe = Array();
    $paire = Array();
    $paire['lib'] = "Prêt, communication";
    $paire['code_zone'] = "tab_pretcomm";
    $paire['id']  = "2, 5, 9, 6";
    $tab_groupe[] = $paire;
    
    $paire = Array();
    $paire['lib'] = "Accueil";
    $paire['code_zone'] = "tab_acc";
    $paire['id']  = "7";
    $tab_groupe[] = $paire;
    
    $paire = Array();
    $paire['lib'] = "Renseignements";
    $paire['code_zone'] = "tab_rens";
    $paire['id']  = "1, 4, 8";
    $tab_groupe[] = $paire;
    
    $paire = Array();
    $paire['lib'] = "Tuteurs";
    $paire['code_zone'] = "tab_tuteur";
    $paire['id']  = "3, 10";
    $tab_groupe[] = $paire;
    titre_page($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, "html");
    print '<div id="tabs_stats">';
    print "<ul>\n";
    # On va afficher la liste qui nous sert à faire les onglets
    foreach ($tab_groupe as $paire)
    {
      print '<li><a href="#'.$paire['code_zone'].'">'.$paire["lib"].'</a></li>'."\n";
    }
    print "</ul>\n";
    
    foreach ($tab_groupe as $paire)
    {
      print "<div id='".$paire["code_zone"]."'>\n";
      print "<div style='text-align:right'><a href='stats_pdf.php?code_zone=".$paire["code_zone"]."&type=$type&semaine=$semaine&mois=$mois&annee=$annee&intervalle_deb=$intervalle_deb&intervalle_fin=$intervalle_fin' target='_blank'>Exporter en PDF</a></div>";
      temps_presence($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, $paire["id"], "html");
#      permanences($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, $paire["id"], "html");
      fermetures($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, 1, $paire["id"], "html"); # Samedi
      fermetures($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, 0, $paire["id"], "html"); # Femetures semaines


      print "</div> <!-- Fin contenu d'un onglet ".$paire["code_zone"]." -->\n";
    }
    
    print '</div> <!-- tabs_stats -->';
    
    # On va ajouter la zone qui donne des informations sur le contenu des statistiques
      temps_par_groupe($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin);    
    print "<div id='zone_note_stat'>\n";
    if (file_exists("info_stat.txt"))
    {
      $handle = fopen("info_stat.txt", "r");
      $taille = filesize("info_stat.txt");
      $taille++;
      $contenu = fread($handle, $taille);
      print $contenu;
      print "<br/><br/><div style='font-size:small; text-align:right; font-weight:normal'><a style='color:black; font-style:italic' href='modif_note_stat.php'>Modifier cette note</a></div>";
    }
    else
    {
      print "<br/><div style='font-size:small; text-align:right'><a href='modif_note_stat.php'>Créer une note pour cette page</a></div>";
    }
    
    print "</div> <!-- zone_note_stat -->\n";
    print "</div> <!-- #stats -->\n";
  }
?>
  </div> <!-- #global -->

<?php
  function affiche_semaine($annee_page, $semaine_page, $type)
  {
    $res = SQL("select distinct (substring(date, 1, 4)) as annee, week(date, 1) as semaine, subdate(date, INTERVAL weekday(date) DAY) as lundi, adddate(date, INTERVAL 6-weekday(date) DAY) as samedi from planning order by annee, semaine;");
    $annee_courante = "";
    while ($row = mysql_fetch_assoc($res))
    {
      $semaine  = $row["semaine"];
      $annee    = $row["annee"];
      $lundi    = $row["lundi"];
      $samedi    = $row["samedi"];
      
      $lundi  = substr($lundi, 8, 2)."/".substr($lundi, 5, 2);
      $samedi = substr($samedi, 8, 2)."/".substr($samedi, 5, 2);
      
      
      if ($annee != $annee_courante)
      {
        if ($annee_courante != "")
        {
          print "</ul>";
        }
        if ($annee == $annee_page)
        {
          print "<li><b>$annee</b><ul>\n";
        }
        else
        {
          print "<li>$annee<ul>\n";
        }
        
        $annee_courante = $annee;
      }
      
      // On va chercher pour la date 
      
      if ( ($semaine == $semaine_page) && ($annee = $annee_page) )
      {
        print "<li><b>Semaine $semaine</b> <span class='date_semaine'>($lundi - $samedi)</span></li>\n";
      }
      else
      {
        print "<li><a href='stats.php?type=semaine&annee=$annee&semaine=$semaine'>Semaine $semaine</a> <span class='date_semaine'>($lundi - $samedi)</span></li>\n";
      }
    }
  }
  
  function affiche_mois($annee_page, $mois_page, $type)
  {
    $res = SQL("select distinct (substring(date, 1, 4)) as annee, substring(date, 6, 2) as mois from planning order by annee, mois;");
    $annee_courante = "";
    while ($row = mysql_fetch_assoc($res))
    {
      $mois   = $row["mois"];
      $annee  = $row["annee"];
      
      if ($annee != $annee_courante)
      {
        if ($annee_courante != "")
        {
          print "</ul>";
        }
        print "<li>$annee<ul>\n";
        $annee_courante = $annee;
      }
      if ( ($mois == $mois_page) and ($annee == $annee_page) )
      {
        print "<li><b>".traduit_code_mois($mois)."</b></li>\n";
      }
      else
      {
        print "<li><a href='stats.php?type=mois&annee=$annee&mois=$mois'>".traduit_code_mois($mois)."</a></li>\n";
      }
    }
  }

  function affiche_annees($annee, $type)
  {
    $res = SQL("select distinct (substring(date, 1, 4)) as annee from planning order by annee;");
    print "<ul>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      if ($row["annee"] == $annee)
      {
        print "<li><b>".$row["annee"]."</b></li>\n";
      }
      else
      {
        print "<li><a href='stats.php?type=$type&annee=".$row["annee"]."'>".$row["annee"]."</a></li>\n";
      }
    }
    print "</ul>\n";    
  }

  function lien_menu_stats($lib, $code, $code_courant, $tiret)
  {
    if ($code == $code_courant)
    {
      print "<b>\n";
      print "<a href='stats.php?type=$code'>$lib</a>";
      print "</b>\n";
    }
    else
    {
      print "<a href='stats.php?type=$code'>$lib</a>";
    }
    
    if ($tiret == 1)
    {
      print " - ";
    }
    else
    {
      // Fin du menu, on fait un clear all
      print "<br style='clear:both'/>";
    }
  }
?>