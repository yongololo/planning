<?php
  # Cette page va proposer à l'utilisateur de superposer à une semaine existante
  # une semaine type définie par ailleurs.
  if (isset($_GET["sem_type"]))
  {
    $sem_type = $_GET["sem_type"];
  }
  
  include("include/connect.php");
  include("include/utils.php");
  include("include/haut.php");
  
  if (isset($_GET['semaine']))
  {
   $semaine = $_GET['semaine'];
  }
  else
  {
   $semaine = "";
  }
  
  if (isset($_GET['annee']))
  {
   $annee = $_GET['annee'];
  }
  else
  {
   $annee = "";
  }
  
  if (isset($_GET['valid_ok']))
  {
    $is_ok = $_GET['valid_ok'];
  }
  else
  {
    $is_ok  = "";
  }
  print "<br style='clear:both'/>\n";
  print "<br style='clear:both'/>\n";
  print "<br style='clear:both'/>\n";
  if (isset($sem_type))
  {
    # On va récupérer les horaires et les sections
    $tab_horaires = Array();
    $tab_sections = Array();
    
    $resA = SQL("select * from horaires");
    while ($rowA = mysql_fetch_assoc($resA))
    {
      $id = $rowA["id"];
      $lib = $rowA["heures"];
      $tab_horaires[$id] = $lib;
    }
    
    $resA = SQL("select * from sections");
    while ($rowA = mysql_fetch_assoc($resA))
    {
      $id = $rowA["id"];
      $lib = $rowA["nom"];
      $tab_sections[$id] = $lib;
    }
    
    
    $tab_doublons = Array();
    
    $nb_jour_doublons = 0;
    
    # On va regarder les créneaux qui font doublons :
    $tab_semaine = get_jours_semaines($semaine, $annee);
    for ($i = 0; $i < sizeof($tab_semaine); $i++)
    {
      $jour_dest  = date("Y-m-d", $tab_semaine[$i]);
      $jour_src = $i + 1;
      
      $req = "select * from planning_type, planning where planning_type.num_jour = '$jour_src' and planning.date='$jour_dest' and planning_type.id_semaine_type = $sem_type and planning.id_section = planning_type.id_section and planning.id_horaire = planning_type.id_horaire and planning_type.id_perso != '' and planning.id_perso != '' and planning.position = planning_type.position order by planning.id_horaire, planning.id_section";

      $doublon = SQL($req);
      $nb_doublons = mysql_numrows($doublon);
      if ($nb_doublons != 0)
      {
        if ($is_ok != 1)
        {
          if ($nb_jour_doublons  == 0)
          {
            print "<span style='font-weight:bold;color:red'>Attention, certains créneaux définis dans la semaine type sont déjà présents dans la semaine, ils ne seront pas écrasés !</span><br/><br/>";
          }
          $nb_jour_doublons++;
          
          print "Créneaux déjà remplis le <b>".traduit_code_jour($jour_src)."</b><br/>";
          print "<ul>\n";
          while ($row_doublons = mysql_fetch_assoc($doublon))
          {
            $id_s = $row_doublons["id_section"];
            $id_h = $row_doublons["id_horaire"];
            print "<li>".$tab_horaires[$id_h].", ".$tab_sections[$id_s]."</li>";
          }
          print "</ul>\n";
        }
        else
        {
          # On doit stocker les champs qui font doublons
          while ($row_doublons = mysql_fetch_assoc($doublon))
          {
            $id_s = $row_doublons["id_section"];
            $id_h = $row_doublons["id_horaire"];
            $id_p = $row_doublons["id_perso"];
            $num_jour = $row_doublons["num_jour"];
            $tab_doublons[$num_jour][$id_h][$id_s] = $id_p ;
          }
        }
      }
    }
    
    if ($is_ok != 1)
    {
      # Si ce n'est pas encore fait, on va demander la confirmation de fusion
      print "<br/><br/>Confirmez-vous la superposition de cette semaine type ?<br/>";
      print "<a href='supp_planning.php?annee=$annee&semaine=$semaine&sem_type=$sem_type&valid_ok=1'>OUI</a> - <a href='supp_planning.php?annee=$annee&semaine=$semaine'>NON</a>";
    }
    else
    {
      $semaine_type = $_GET["sem_type"];
      $tab_semaine = get_jours_semaines($_GET["semaine"], $_GET["annee"]);
      for ($i = 0; $i < sizeof($tab_semaine); $i++)
      {
        
        
        $jour_dest  = date("Y-m-d", $tab_semaine[$i]);
        $res = SQL("select * from planning_type where id_semaine_type = $semaine_type and num_jour = ".($i + 1)." and id_perso != '';");
        while ($row_hor = mysql_fetch_assoc($res))
        {
          $id_section = $row_hor["id_section"];
          $id_perso   = $row_hor["id_perso"];
          $id_horaire = $row_hor["id_horaire"];
          $position   = $row_hor["position"];
          $num_jour   = $row_hor["num_jour"];
          
          if (isset($tab_doublons[$num_jour][$id_horaire][$id_section]))
          {
//            print "Déjà quelqu'un à l'horaire : $num_jour // $id_horaire pour la section $id_section<br/>";
          }
          else
          {
            SQL("insert into planning (`num_semaine`, `date`, `id_section`, `id_perso`, `id_horaire`, `position`) values ('$semaine', '$jour_dest', '$id_section', '$id_perso', '$id_horaire', '$position')");            
          }
         
         //  
        }
      }
      print "Fusion effectuée avec succès : <a href='planning.php?annee=$annee&semaine=$semaine&action=modification'>Revenir au planning</a>";
    }
  }
  else
  {
    # On n'a pas défini de semaine type, on va donc proposer ce que l'on peut trouver
    $res = SQL("select * from semaines_types order by lib");

    print "Sélectionner la semaine type à supperposer à la semaine $semaine ($annee) :";
    print "<ul>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      $id = $row["id"];
      $lib = $row["lib"];
      
      print "<li><a href='supp_planning.php?semaine=$semaine&annee=$annee&sem_type=$id'>$lib</a></li>\n";
    }
    print "</ul>\n";
  }

  include("include/bas.php");
?>
