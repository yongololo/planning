<?php
  include("include/connect.php");
  include("include/utils.php");
  $code_page = "planning";
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
  
  if (isset($_GET['action']))
  {
    $action = $_GET['action'];
  }
  else
  {
    $action = "";
  }
  
  if (isset($_GET['jour']))
  {
    $jour = $_GET['jour'];
  }
  else
  {
    $jour = "";
  }
  
  if (isset($_GET['stamp_modif']))
  {
    $stamp_modif = $_GET['stamp_modif'];
  }
  else
  {
    $stamp_modif = "";
  }
  
  $nb_semaines_par_ligne = 5;
  
  # Si l'utilisateur n'a pas saisi d'année, on va lui proposer
  # par défaut le planning complet de l'année en cours
  if ($annee == "")
  {
    $annee = date("Y");
  }
  
  //###############################################################//
  // Affichage d'une année complète pour permettre la modification //
  //###############################################################//
  if ($semaine == "")
  {
    print "<h1><a href='planning.php?annee=".($annee - 1)."'>&lt;&lt;</a> Plannings de $annee <a href='planning.php?annee=".($annee + 1)."'>&gt;&gt;</a></h1>";
    print "<a href='planning_type.php'>Gestion des plannings type</a>";
    print "<br style='clear:both'/>";
    # On doit commencer par lister l'état des semaines pour l'année sélectionnée
    $semaines = array();
    $result = SQL("select distinct num_semaine from planning where date like '$annee%';");
   
    while ($res = mysql_fetch_assoc($result))
    {
      $semaines[$res["num_semaine"]] = 1;
    }

    # Aucune semaine n'est indiquée, on doit donc afficher toute l'année.
    print "<table id='planning_annuel'><tr>\n";
    for ($i = 1; $i <= 52; $i++)
    {
      # On va afficher un lien modifier ou un lien créer selon que la semaine existe déjà où est à créer
      $tmp = get_jours_semaines($i, $annee);
      
      # On va regarder si la case concerne la semaine en cours.
      # Si c'est le cas on va ajouter une classe
      $classe_semaine_courante = "";
      $auj = strtotime(date("Y-m-d"));
      if ( ($auj >= $tmp[0]) && ($auj < $tmp[sizeof($tmp) -1]) )
      {
        $classe_semaine_courante = "semaine_courante";
      }
      
      if (isset($semaines[$i]))
      {
        print "\t\t<td class='semaine_exist $classe_semaine_courante '>";
        print date("d/m", $tmp[0])." - ".date("d/m", $tmp[sizeof($tmp) - 1])." (s. $i)<br/>";
        print "<a href='planning.php?annee=$annee&semaine=$i&action=modification'>Modifier</a>";
      }
      else
      {
        print "\t\t<td class='semaine_inexist $classe_semaine_courante'>";
        print "s. $i (".date("d/m", $tmp[0])." - ".date("d/m", $tmp[sizeof($tmp) - 1]).")<br/>";
        print "<a href='planning.php?annee=$annee&semaine=$i&action=creation'>Créer</a>";
      }
      print "</td>\n";
      if ( !($i % $nb_semaines_par_ligne) )
      {
        print "</tr>\n";
        print "\t<tr>\n";
      }
    }
    # On doit rajouter une case fusionnée pour éviter
    # que la dernière ligne ne soit pas complète
    $fusion = ($nb_semaines_par_ligne - (52 % $nb_semaines_par_ligne) );
    print "<td colspan='$fusion'>&nbsp;</td>";
    print "</tr></table>";
  }
  elseif ($action == "creation")
  {
    print "<h1>Création du planning de la semaine $semaine ($annee)</h1>";
    print "<br style='clear:both'/>";
    print "<div style='text-align:center'>";
    # On va afficher la liste des semaines pour lesquelles on a déjà quelque chose
    $result = SQL("select distinct substring(date, 1, 4) as annee, num_semaine from planning order by annee, num_semaine;");
    print "<form method='get' action='planning.php'>\n";
    print "<input type='hidden' name='annee' value='$annee'/>\n";
    print "<input type='hidden' name='semaine' value='$semaine'/>\n";
    print "<input type='hidden' name='action' value='modification'/>\n";

    ### Depuis un planning existant ###
    print "<h2>Création depuis un planning existant</h2>\n";
    print "<select name='derivation_from'>\n";
    print "<option value=''></option>";
    while ($row = mysql_fetch_assoc($result))
    {
      $annee_deriv  = $row["annee"];
      $semaine_deriv   = $row["num_semaine"];
      
      print "\t<option value='".$annee_deriv."_".$semaine_deriv."'/>Année $annee_deriv, semaine $semaine_deriv</option>\n";
    }
    print "</select>\n";
    print "<input type='submit' value='Valider'/>";

    
    ### Création depuis un planning type ###
    print "<h2>Création depuis un planning type</h2>";
    $result = SQL("select * from semaines_types");
    print "<select name='derivation_from_type'>\n";
    print "<option value=''></option>";
    while ($row = mysql_fetch_assoc($result))
    {
      print "<option value='".$row["id"]."'>".$row["lib"]."</option>";
    }
    print "</select>\n";
    print "<input type='submit' value='Valider'/>";
    print "</form>";    
    ### Depuis un planning vierge
    print "<h2><a href='planning.php?annee=".$annee."&semaine=".$semaine."&action=modification'>";
    print "Création à partir d'un planning vierge</a></h2>";
    print "</div>\n";
  }
  //#################################################//
  // Affichage d'un jour seulement pour modification //
  //#################################################//
  elseif ($action == "modification")
  {
    # SMA 201207 : On va commencer par regarder si l'on est pas sur une semaine d'été
    $res = SQL("select * from semaines_ete where annee = $annee and num_sem = $semaine");
    $horaire_ete = mysql_numrows($res);
    
    # On va afficher la liste des jours de la semaine pour permettre une sélection
    $tab_semaine = get_jours_semaines($semaine, $annee);    

    if ( (isset($_GET["derivation_from"])) && ($_GET["derivation_from"] != "") )
    {
      $tmp_deriv = preg_split("/_/", $_GET["derivation_from"]);
      
      $annee_deriv    = $tmp_deriv[0];
      $semaine_deriv  = $tmp_deriv[1];

      $tab_semaine_deriv = get_jours_semaines($semaine_deriv, $annee_deriv);
      for ($i = 0; $i < sizeof($tab_semaine_deriv); $i++)
      {
        $jour_dest  = date("Y-m-d", $tab_semaine[$i]);
        $jour_deriv = date("Y-m-d", $tab_semaine_deriv[$i]);
        $res = SQL("select * from planning where date = '$jour_deriv'");
        while ($row = mysql_fetch_assoc($res))
        {
          $id_section = $row["id_section"];
          $id_perso   = $row["id_perso"];
          $id_horaire = $row["id_horaire"];
          $position   = $row["position"];
          
          SQL("insert into planning (`num_semaine`, `date`, `id_section`, `id_perso`, `id_horaire`, `position`) values ('$semaine', '$jour_dest', '$id_section', '$id_perso', '$id_horaire', '$position')");
        }
      }
    }
    # Dérivation depuis un planning type
    elseif ( (isset($_GET["derivation_from_type"])) && ($_GET["derivation_from_type"] != "") )
    {
      $semaine_type = $_GET["derivation_from_type"];
      $tab_semaine = get_jours_semaines($_GET["semaine"], $_GET["annee"]);
      for ($i = 0; $i < sizeof($tab_semaine); $i++)
      {
        $jour_dest  = date("Y-m-d", $tab_semaine[$i]);
        $res = SQL("select * from planning_type where id_semaine_type = $semaine_type and num_jour = ".($i + 1).";");
        while ($row_hor = mysql_fetch_assoc($res))
        {
          $id_section = $row_hor["id_section"];
          $id_perso   = $row_hor["id_perso"];
          $id_horaire = $row_hor["id_horaire"];
          $position   = $row_hor["position"];
          
          SQL("insert into planning (`num_semaine`, `date`, `id_section`, `id_perso`, `id_horaire`, `position`) values ('$semaine', '$jour_dest', '$id_section', '$id_perso', '$id_horaire', '$position')");
        }
      }
    }
        
    if ($stamp_modif == "")
    {
      # On est en règle général sur la modification d'une semaine
      # le stamp_modif va donc correspondre au premier jour de la semaine
      $stamp_modif = $tab_semaine[0];
    }
    
    # Ici on a une semaine et on veut la modifier
    print "<h1>Modification du ".date('d/m/Y', $stamp_modif)." (sem. $semaine)</h1>\n";
    if ($horaire_ete)
    {
      print "<p style='text-align:center; font-style:italic; font-weight:bold; color:#FF9900'>Horaires d'été</p>";
    }
    # On va proposer de faire un import d'une semaine type sur cette semaine.
    if (!$horaire_ete)
    {
      print "<a href='supp_planning.php?semaine=$semaine&annee=$annee'>Superposer une semaine type sur cette semaine</a>\n";
    }
    
    // On va stocker le stamp_modif dans un span caché pour
    // pouvoir l'utiliser plus facilement en javascript
    print "<span id='stamp_modif' title='" . $stamp_modif . "'/>";
    
    $jour_courant = "";
    
    # On va ensuite afficher une liste de liens permettant d'accéder directement aux jours
    print "<div id='liste_jours'>\n";
    for ($i = 0; $i < sizeof($tab_semaine); $i++)
    {
      $datestamp = $tab_semaine[$i];
      $nom_jour = get_nom_jour_from_datestamp($datestamp);
      if ($datestamp == $stamp_modif)
      {
        print "<div class='un_jour'>$nom_jour&nbsp;";
        print "</div>\n";
        $jour_courant = $nom_jour;
      }
      else
      {
        print "<div class='un_jour'><a href='planning.php?annee=$annee&semaine=$semaine&action=modification&stamp_modif=$datestamp'>$nom_jour</a></div>\n";
      }
    }
    
    print "</div> <!-- Fin liste_jours -->\n";
    
    
    # On va ensuite afficher la journée dans son intégralité en proposant sur chaque case
    # la possibilité de mofifier le contenu sans recharger (via AJAX)
    $liste_sections = SQL("select * FROM sections ORDER BY `ordre` ASC");
    $nb_sections    = mysql_num_rows($liste_sections);
    
    if ($horaire_ete)
    {
      $liste_horaires = SQL("SELECT * FROM horaires WHERE id in (select distinct id_horaire_ete from jours_horaires where jour = '$jour_courant' and id_horaire_ete != 0)");  
    }
    else
    {
      $liste_horaires = SQL("SELECT * FROM horaires WHERE id in (select distinct id_horaire from jours_horaires where jour = '$jour_courant' and id_horaire != 0)");      
    }

    $nb_horaires    = mysql_num_rows($liste_horaires);
    
    print "<div id='debug_zone' style='float:left'>&nbsp;</div>";
    # On va afficher le tableau
    print "\n\n<table id='planning_jour'>\n";
    
    $tab_horaires = array();
    
    # Affichage des en-têtes horaires
    print "<tr>\n";
    # Case vide qui se place au dessus du nom des sections
    print "<td>".date('d/m/Y', $stamp_modif);
    print "<br/><a href='../visu_conges.php?stamp_modif=$stamp_modif&annee=$annee&semaine=$semaine&admin=1' style='font-size:small; font-weight:bold; color:red'>Personnes en congés</a>\n";
    print "</td>\n"; 
    for ( $i_horaire = 0; $i_horaire < $nb_horaires; $i_horaire++)
    {
      $row_horaire = mysql_fetch_array($liste_horaires);
      echo "<th class='entete_planning' id='col_".$row_horaire["id"]."'>".$row_horaire['heures']."</th>\n";
      $tab_horaires[] = $row_horaire["id"];
    }
    echo "</tr>\n";

    # On doit récupérer toutes les informations qui nous intéressent pour l'occupation des espaces.
    # on va mettre ça dans une structure à laquelle on accèdera à chaque fois que l'on aura besoin
    $occupation = Array();
    $req = "select * from planning, personnel where planning.id_perso = personnel.id and date='".date("Y-m-d", $stamp_modif)."' order by position;";
   
    $liste_occupations = SQL($req);
    while ($row = mysql_fetch_assoc($liste_occupations))
    {
      $personne = array();
      $personne["id"]   = $row["id_perso"];
      $personne["nom"]  = $row["prenom"];
      
      $occupation[$row["id_section"]][$row["id_horaire"]][$row["position"]] = $personne;
    }

    # On va ensuite afficher les cases section par section
    for ( $i_section = 0; $i_section < $nb_sections; $i_section++ )
    {
      $row_section  = mysql_fetch_array($liste_sections);
      
      $s_nom      = $row_section["nom"];
      $s_couleur  = $row_section["couleur"];
      $s_id       = $row_section["id"];
      $s_max_pers = $row_section["max_pers"];
      $s_class    = $row_section["class"];

      print "<tr class='".$s_class."'>";
      print "<td width='10%' class='column1'>$s_nom</td>\n";
      # On va ensuite traverser les horaires
      foreach ($tab_horaires as $id_horaire)
      {
        # On donne à cette case un identifiant qui stocke l'identifiant 
        print "<td width='12%' id='".$s_id."_".$id_horaire."'>";
        print "<a href='#' class='modif_case'><img src='img/modify.png' alt='modifier' title='modifier' style='float:right'/></a>\n";
        if ( (isset($occupation[$s_id])) && (isset($occupation[$s_id][$id_horaire])) )
        {
          print "<div class='liste_perso'>\n";
          print "<ul class='ul_h_".$id_horaire." ul_s_".$s_id."'>\n";
          foreach ($occupation[$s_id][$id_horaire] as $paire)
          {
            print "<li class='perso_".$paire["id"]."'>".$paire["nom"]."</li>";
          }
          print "</ul>\n";
          print "</div>\n";
        }
        print "</td>\n";
      }
      print "</tr>";
    }
    
    // On ajouter une ligne vide
    print "<tr><td colspan='".(sizeof($tab_horaires) + 1)."'>&nbsp;</td></tr>\n";
    
    // On va lire les observations pour ce jour là
    $req = "select * from Observations where date='".date("Y-m-d", $stamp_modif)."';";
    $res = SQL($req);
    $tab_obs = Array();
    while ($row = mysql_fetch_assoc($res))
    {
      $tab_obs[$row["Id-horaire"]] = $row["Observation"];
    }
    
    print "<tr style='font-size:0.8em'>\n";
    print "<td>Observations</td>";
    foreach ($tab_horaires as $id_horaire)
    {
      print "<td>";
      print "<a href='#' class='case_obs' id='caseobs_$id_horaire'><img src='img/modify.png' alt='modifier' title='modifier' style='float:right'/></a>\n";
      if (isset($tab_obs[$id_horaire]))
      {
        print "<span class='obs'>".$tab_obs[$id_horaire]."</span></td>";
      }
      else
      {
        print "<span class='obs'>&nbsp;</span></td>";
      }
    }
    print "</tr>";
    print "</table> <!-- #planning_jour -->\n";
    print "<div style='text-align:center'>\n";
    $query_string = $_SERVER['QUERY_STRING'];
    $query_string = str_replace("modification", "consultation", $query_string);
    print "<a target='_blank' href='../index.php?".$query_string ."&format=pdf&stamp_modif=$stamp_modif'><img src='../img/pdf.png' alt='PDF' style='border:0px' title='PDF'/></a>";
    print "</div>\n";
  }

  include("include/bas.php");
?>
