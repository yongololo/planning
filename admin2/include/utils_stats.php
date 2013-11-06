<?php
  function titre_page($type, $annee, $semaine, $mois, $debut, $fin, $format_sortie)
  {
    $titre = "";
    if ($type == "annee")
    {
      $titre = "Statistiques pour l'année $annee";
    }
    elseif ($type == "mois")
    {
      $titre = "Statistiques pour ".traduit_code_mois($mois)." $annee";
    }
    elseif ($type == "semaine")
    {
      $titre = "Statistiques pour la semaine $semaine ($annee)";
    }
    elseif ($type == "periode")
    {
      $titre = "Statistiques du $debut au $fin";
    }

    if ($format_sortie == "pdf")
    {
      return $titre;
    }
    else
    {
      print "<br/>";
      print "<span id='titre_page_stats'>$titre</span>";
      print "<br/>";
      print "<br/>";
    }
  }
  
  function fermetures($type, $annee, $semaine, $mois, $debut, $fin, $samedi = 0, $sections, $format_sortie)
  {
    // Variable utilisée pour sortir un tableau (utile pour le PDF)
    $tableau_sortie = Array();
    $tableau_sortie[0]["nom"] = "Nom";
    $tableau_sortie[0]["val"] = "Nombre de fermetures";
    $req_periode = req_periode($type, $annee, $semaine, $mois, $debut, $fin);
    $req = "select personnel.*, count(*) as nb from personnel, horaires, planning where personnel.id = planning.id_perso and planning.id_horaire = horaires.id and horaires.fermeture_bool = 1";
    if ($samedi == 0)
    {
      $req = $req . " and DAYNAME(date) != 'Saturday' ";  
    }
    else
    {
      $req = $req . " and DAYNAME(date) = 'Saturday' ";  
    }
    
    $req = $req . "and $req_periode and planning.id_section in ($sections) group by personnel.id order by nb desc";
//    $requete = "SELECT personnel.*, COUNT(*) as nb_fermeture FROM personnel LEFT JOIN horaires ON horaires.fermeture = 'oui' LEFT JOIN planning ON planning.id_perso = personnel.id WHERE planning.id_horaire = '10' $where_sem_plus $where_mois_plus $where_periode GROUP BY personnel.prenom ORDER BY nb_fermeture DESC";

    $res = SQL($req);
    $taille_max = 400;
    $nb_max = "";
    
    if ($format_sortie == "html")
    {
      if ($samedi == 0)
      {
        print "<h2>Fermetures (semaine) <a href='#' class='lien_cache' id='ferm_semaine'>(masquer)</a></h2>\n";
      }
      else
      {
        print "<h2>Samedi <a href='#' class='lien_cache' id='ferm_semaine'>(masquer)</a></h2>\n";
      }
      print "<table>\n";      
    }
    $total = 0;
    while ($row = mysql_fetch_assoc($res))
    {
      if ($nb_max == "")
      {
        $nb_max = $row["nb"];
      }
      
      if ($format_sortie == "html")
      {
        print "<tr><td>".$row["prenom"]." ".$row["nom"]."</td>\n";
        print "<td>\n";
        print "<div class='barre_graphique' style='width:" . ($row["nb"] * $taille_max / $nb_max)."px;'/>";
        print $row["nb"];
        print "</td>\n";
        print "</tr>\n";
      }
      else
      {
        $paire = Array();
        $paire["nom"]   = $row["prenom"]." ".$row["nom"];
        $paire["val"]   = $row["nb"];
        $tableau_sortie[] = $paire;
      }
      $total += $row["nb"];
    }
    
    if ($format_sortie == "html")
    {
      print "<tr style='font-weight:bold'><td>Total : </td><td>$total</td></tr>\n";    
      print "</table>";
    }
    else
    {
      return $tableau_sortie;
    }
  }


  function temps_presence($type, $annee, $semaine, $mois, $debut, $fin, $sections, $format_sortie)
  {
    $tableau_sortie = Array();
    $tableau_sortie[0]["nom"] = "Nom";
    $tableau_sortie[0]["val"] = "Durée";
    $req_periode = req_periode($type, $annee, $semaine, $mois, $debut, $fin);
    $req = "select personnel.*, sum(horaires.temps) as duree from personnel, planning, horaires where personnel.id = planning.id_perso and planning.id_horaire = horaires.id and $req_periode and planning.id_section in ($sections) ";
    # $req .= " and planning.id_horaire != 11 "; # On doit faire attention à ne pas compter les samedi ici, ils bénéficient d'un traitement à part
    $req .= " group by personnel.id order by duree desc, nom";
    
    $res = SQL($req);
    $taille_max = 400;
    $duree_max = "";
    if ($format_sortie == "html")
    {
      print "<h2>Temps passé en service public <a href='#' class='lien_cache' id='ferm_semaine'>(masquer)</a></h2>\n";
      print "<table>\n";      
    }
    $total = 0;
    while ($row = mysql_fetch_assoc($res))
    {
      if ($duree_max == "")
      {
        $duree_max = $row["duree"];
      }
      # print "<li>".$row["prenom"]." ".$row["nom"]." ==> ".$row["nb"]."</li>";
      if ($format_sortie == "html")
      {
        print "<tr><td>".$row["prenom"]." ".$row["nom"]."</td>\n";
        print "<td>\n";
        print "<div class='barre_graphique' style='width:" . ($row["duree"] * $taille_max / $duree_max)."px;'/>";
        print formate_heures($row["duree"]);
        print "</td>\n";
        print "</tr>\n";
      }
      else
      {
        $paire = Array();
        $paire["nom"] = $row["prenom"]." ".$row["nom"];
        $paire["val"] = formate_heures($row["duree"]);
        $tableau_sortie[] = $paire;
      }
      $total += $row["duree"];
    }
    
    if ($format_sortie == "html")
    {
      print "<tr style='font-weight:bold'><td>Total : </td><td>".formate_heures($total)."</td></tr>\n";
      print "</table>";
    }
    else
    {
      return $tableau_sortie;
    }
  }
  
  function permanences($type, $annee, $semaine, $mois, $debut, $fin, $sections, $format_sortie)
  {
    $tableau_sortie = Array();
    $tableau_sortie[0]["nom"] = "Nom";
    $tableau_sortie[0]["val"] = "Nombre de permanences";
    
    $req_periode = req_periode($type, $annee, $semaine, $mois, $debut, $fin);
    $req = "select personnel.*, count(*) as nb from personnel, planning where personnel.id = planning.id_perso and $req_periode and planning.id_section in ($sections) ";
    $req .= " and planning.id_horaire != 11 "; # On doit faire attention à ne pas compter les samedi ici, ils bénéficient d'un traitement à part
    $req .= " group by personnel.id order by nb desc, nom";
        
    $res = SQL($req);
    $taille_max = 400;
    $nb_max = "";
    
    if ($format_sortie == "html")
    {
      print "<h2>Permanences <a href='#' class='lien_cache' id='ferm_semaine'>(masquer)</a></h2>\n";
      print "<table>\n";
    }
    $total = 0;
    while ($row = mysql_fetch_assoc($res))
    {
      if ($nb_max == "")
      {
        $nb_max = $row["nb"];
      }
      # print "<li>".$row["prenom"]." ".$row["nom"]." ==> ".$row["nb"]."</li>";
      if ($format_sortie == "html")
      {
        print "<tr><td>".$row["prenom"]." ".$row["nom"]."</td>\n";
        print "<td>\n";
        print "<div class='barre_graphique' style='width:" . ($row["nb"] * $taille_max / $nb_max)."px;'/>";
        print $row["nb"];
        print "</td>\n";
        print "</tr>\n";
      }
      elseif ($format_sortie == "pdf")
      {
        $paire = Array();
        $paire["nom"] = $row["prenom"]." ".$row["nom"];
        $paire["val"] = $row["nb"];
        $tableau_sortie[] = $paire;
      }
      $total += $row["nb"];
    }
    if ($format_sortie == "html")
    {
      print "<tr style='font-weight:bold'><td>Total : </td><td>$total</td></tr>\n";
      print "</table>";
    }
    elseif ($format_sortie == "pdf")
    {
      return $tableau_sortie;
    }
  }

  function temps_par_groupe($type, $annee, $semaine, $mois, $debut, $fin)
  {
    $req_periode = req_periode($type, $annee, $semaine, $mois, $debut, $fin);
    # On va commencer par liste les groupes
    $res_groupes = SQL("select * from groupes");
    print "<h2>Volume horaire par groupe</h2>";
    print "<ul>";
    while ($row_groupes = mysql_fetch_assoc($res_groupes))
    {
      $id_groupe = $row_groupes["id"];
      $lib_groupe = $row_groupes["lib"];
      $sections_groupe = $row_groupes["sect"];
      
      $req_sections = "";
      $tab_sections = preg_split("/,/", $sections_groupe);
      foreach ($tab_sections as $une_section)
      {
        if ($req_sections == "")
        {
          $req_sections = "'".$une_section."'";
        }
        else
        {
          $req_sections .= ", '".$une_section."'";
        }
      }
      
      $req_temps = "select sum(horaires.temps) as duree from personnel, planning, horaires where personnel.id = planning.id_perso and planning.id_horaire = horaires.id and $req_periode and planning.id_section in ($req_sections);";
      $res_un_groupe = SQL($req_temps);
      $row_un_groupe = mysql_fetch_assoc($res_un_groupe);
      print "<li>$lib_groupe : ";
      print formate_heures($row_un_groupe["duree"]);
      print "</li>";
    }
    print "</ul>";
  }

  function req_periode($type, $annee, $semaine, $mois, $debut, $fin)
  {
    $out = "";
    if ($type == "annee")
    {
      $out = "year(planning.date) = '$annee'";
    }
    elseif ($type == "semaine")
    {
      $out = "year(planning.date) = '$annee' and week(planning.date, 1) = '$semaine'";
    }
    elseif ($type == "mois")
    {
      $out = "planning.date like '$annee-$mois%'";
    }
    elseif ($type == "periode")
    {
      $debut_sql = substr($debut, 6, 4)."-".substr($debut, 3, 2)."-".substr($debut, 0, 2);
      $fin_sql = substr($fin, 6, 4)."-".substr($fin, 3, 2)."-".substr($fin, 0, 2);
      $out = "planning.date between '$debut_sql' and '$fin_sql'";
    }
    
    $out = $out." and planning.position = '0'";
    
    return $out;
  }

?>