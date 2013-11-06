<?php
  include("include/connect.php");
  include("include/utils.php");
  $code_page = "planning_type";
  include("include/haut.php");
  
  if (isset($_GET["action"]))
  {
    $action = $_GET["action"];
  }
  else
  {
    $action = "";
  }

  print "<h1>Semaine type</h1>";
  print "<br style='clear:both'/>";
  if ($action == "")
  {
    print "<a href='planning_type.php?action=ajout'>Créer une nouvelle semaine</a>";
    # On va juste lister les semaines types
    $res = SQL("select * from semaines_types order by lib;");
    print "<ul>";
    while ($row = mysql_fetch_assoc($res))
    {
      print "<li><a href='planning_type.php?action=modification&semaine_type=".$row["id"]."'>".$row["lib"]."</a></li>";
    }
  }
  elseif ($action == "ajout")
  {
    if (isset($_GET["lib"]))
    {
      # On va commencer par intégrer la semaine type
      $res = SQL("insert into semaines_types (`lib`) values ('".$_GET["lib"]."');");
      $id_semaine_type = mysql_insert_id();

      # On a déjà un libellé inscrit, on va donc faire l'opération d'insertion
      $from = $_GET["derivation_from"];

      if ($from != "VIDE")
      {
        $tmp_deriv = preg_split("/_/", $_GET["derivation_from"]);
        
        $annee_deriv    = $tmp_deriv[0];
        $semaine_deriv  = $tmp_deriv[1];
  
        $tab_semaine_deriv = get_jours_semaines($semaine_deriv, $annee_deriv);
        for ($i = 0; $i < sizeof($tab_semaine_deriv); $i++)
        {
          $jour_source = date("Y-m-d", $tab_semaine_deriv[$i]);
          $num_jour_source = date("N", $tab_semaine_deriv[$i]);
          
          $res = SQL("select * from planning where date = '$jour_source'");
          while ($row = mysql_fetch_assoc($res))
          {
            $id_section = $row["id_section"];
            $id_perso   = $row["id_perso"];
            $id_horaire = $row["id_horaire"];
            $position   = $row["position"];
            SQL("insert into planning_type (`id_semaine_type`, `num_jour`, `id_section`, `id_perso`, `id_horaire`, `position`) values ('$id_semaine_type', '$num_jour_source', '$id_section', '$id_perso', '$id_horaire', '$position')");
          }
        }
      }
      
      # On va ensuite proposer à l'utilisateur d'aller modifier sa semaine type :
      print "La semaine type <i><b>".$_GET["lib"]."</b></i> a bien été créée. Vous pouvez désormais la <a href='planning_type.php?action=modification&semaine_type=".$id_semaine_type."'>modifier</a>.";
    }
    else
    {
      # On va proposer le formulaire permettant de définir la semaine type
      print "<form method='GET'>\n";
      print "<input type='hidden' name='action' value='ajout'/>";
      print "<label>Nom de la semaine type : </label><input type='text' name='lib' value=''/>";
      print "<br/>Créer à partir de : ";
      $result = SQL("select distinct substring(date, 1, 4) as annee, num_semaine from planning order by annee, num_semaine;");
      print "<select name='derivation_from'>\n";
      print "<option value='VIDE'>Semaine vierge</option>";
      while ($row = mysql_fetch_assoc($result))
      {
        $annee_deriv  = $row["annee"];
        $semaine_deriv   = $row["num_semaine"];
        
        print "\t<option value='".$annee_deriv."_".$semaine_deriv."'/>Année $annee_deriv, semaine $semaine_deriv</option>\n";
      }
      print "</select>\n";
      print "<input type='submit'/>";
      print "</form>\n";
    }
  }
  elseif ($action == "modification")
  {
    if (isset($_POST["planning"]))
    {
      $id_semaine_type = $_GET["semaine_type"];
      print "Planning à mettre à jour<br/>";
      # On commence par vider le planning :
      SQL("delete from planning_type where id_semaine_type = $id_semaine_type");
      
      $tab_planning = $_POST["planning"];
      foreach ($tab_planning as $num_jour => $tab_jour)
      {
        foreach ($tab_jour as $id_section => $tab_section)
        {
          foreach ($tab_section as $id_horaire => $tab_heure)
          {
            $position = 0;
            foreach ($tab_heure as $id_perso)
            {
              SQL("insert into planning_type (`id_semaine_type`, `num_jour`, `id_section`, `id_perso`, `id_horaire`, `position`) values ('$id_semaine_type', '$num_jour', '$id_section', '$id_perso', '$id_horaire', '$position')");
              $position++;
            }
          }
        }
      }
    }
   
    
    $semaine_type = $_GET["semaine_type"];

    # Préparation de la semaine type
    $tab_semaine = Array();
    $tab_semaine[1] = "Lundi";
    $tab_semaine[2] = "Mardi";
    $tab_semaine[3] = "Mercredi";
    $tab_semaine[4] = "Jeudi";
    $tab_semaine[5] = "Vendredi";
    $tab_semaine[6] = "Samedi";

    # Préparation du tableau des horaires
    

        
    # Affichage des en-têtes horaires
    
    
    # Case vide qui se place au dessus du nom des sections
    
    print "<form method='post'>";

    foreach ($tab_semaine as $num_jour => $lib_jour)
    {
      print "<input type='submit' value='Valider'/>";
      print "<h2>$lib_jour</h2>";
      print "<table width='100%' border='1px'>";
      $tab_horaires = array();
      
      $liste_horaires = SQL("SELECT * FROM horaires WHERE id in (select distinct id_horaire from jours_horaires where jour = '$lib_jour')");
      $nb_horaires    = mysql_num_rows($liste_horaires);
      print "<tr>\n";
      print "<td>&nbsp;</td>\n"; 
      for ( $i_horaire = 0; $i_horaire < $nb_horaires; $i_horaire++)
      {
        $row_horaire = mysql_fetch_array($liste_horaires);
        echo "<th class='entete_planning' id='col_".$row_horaire["id"]."'>".$row_horaire['heures']."</th>\n";
        $tab_horaires[] = $row_horaire["id"];
      }
      echo "</tr>\n";
      
      # On va ensuite parcourir toutes les sections
      $liste_sections = SQL("select * FROM sections ORDER BY `ordre` ASC");
      $nb_sections    = mysql_num_rows($liste_sections);
      
      # On va récupérer le tableau d'occupation pour ce jour 
      $occupation = Array();
      $req = "select * from planning_type, personnel where planning_type.id_perso = personnel.id and num_jour='$num_jour' and id_semaine_type=$semaine_type order by position;";
   
      $liste_occupations = SQL($req);
      while ($row = mysql_fetch_assoc($liste_occupations))
      {
        $personne = array();
        $personne["id"]   = $row["id_perso"];
        $personne["nom"]  = $row["prenom"];
        
        $occupation[$row["id_section"]][$row["id_horaire"]][$row["position"]] = $personne;
      }

      # On va récupérer tous les collègues
      $liste_personnes = SQL("select * from personnel where actif=1 order by nom, prenom");
      $tab_personnes = Array();
      while ($row_perso = mysql_fetch_assoc($liste_personnes))
      {
        $personne = Array();
        $personne["id"] = $row_perso["id"];
        $personne["nom"] = $row_perso["nom"]." ".$row_perso["prenom"];
        $tab_personnes[] = $personne;
      }

      for ( $i_section = 0; $i_section < $nb_sections; $i_section++ )
      {
        $row_section  = mysql_fetch_array($liste_sections);
        
        $s_nom      = $row_section["nom"];
        $s_couleur  = $row_section["couleur"];
        $s_id       = $row_section["id"];
        $s_max_pers = $row_section["max_pers"];
        $s_class    = $row_section["class"];
  
        print "<tr>";
        print "<td width='10%' class='column1'>$s_nom</td>\n";
        # On va ensuite traverser les horaires
        foreach ($tab_horaires as $id_horaire)
        {
          print "<td style='padding:5px;'>";
          for ($k = 1; $k <= $s_max_pers; $k++)
          {

            # On donne à cette case un identifiant qui stocke l'identifiant 
            
            if ( (isset($occupation[$s_id])) && (isset($occupation[$s_id][$id_horaire])) && (isset($occupation[$s_id][$id_horaire][$k - 1])) )
            {
              print "<select name='planning[$num_jour][$s_id][$id_horaire][]'>";
              print "<option value=''>";
              foreach ($tab_personnes as $id => $une_personne)
              {
                $id_A = $une_personne["id"];
                $nom_A = $une_personne["nom"];
                
                if ($id_A == $occupation[$s_id][$id_horaire][$k - 1]["id"])
                {
                  print "<option value='$id_A' selected>$nom_A</option>";                  
                }
                else
                {
                  print "<option value='$id_A'>$nom_A</option>";                  
                }
              }
              print "</select>";
            }
            else
            {
              print "<select  name='planning[$num_jour][$s_id][$id_horaire][]'>";
              print "<option value=''>";
              foreach ($tab_personnes as $id => $une_personne)
              {
                $id_A = $une_personne["id"];
                $nom_A = $une_personne["nom"];
                
                print "<option value='$id_A'>$nom_A</option>";
              }
              print "</select>";
            }
          }
        }
        print "</td>\n";
        print "</tr>";
      }

      print "</table>";
      
    }


    print "</form>";
  }
?>
