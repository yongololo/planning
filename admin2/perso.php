<?php
  if (isset($_GET["action"]))
  {
    $action = $_GET["action"];
  }
  elseif (isset($_POST["action"]))
  {
    $action = $_POST["action"];
  }
  else
  {
    $action = "";
  }

  include("include/connect.php");
  include("include/utils.php");
  
  $code_page = "perso";
  include("include/haut.php");
  print "<h1>Gestion du personnel</h1>\n";
  print "<br style='clear:both'/>";
  print "<div id='global'>";
  if ($action == "")
  {
    // On va proposer un lien pour la création d'un personnel
    
    // On va afficher la liste des personnels présents avec pour chacun
    $req = "select * from personnel where actif='1' ";
/*    if ($_SESSION["privilege"] == "autogestion")
    {
      $req .= "and id = ".$_SESSION["id"];
    } */
    $req .= " order by nom;";
    
    $res = SQL($req);
    print "<table id='liste_perso'>";
    // On va mettre une première case pour la création d'un collègue
    // Seulement si on est connecté en tant qu'administrateur !
/*    if ($_SESSION["privilege"] == "administrateur")
    { */
      print "<tr>\n";
      print "<td colspan='5' align='right'><a href='perso.php?action=creation&format=public' id='lien_creation'><img src='img/creation.png' alt='Ajouter une personne' title='Ajouter une personne'></a></td>\n";
      print "</tr>\n";
//    }
    
    print "<tr><td colspan='5' align='right'>&nbsp</td></tr>";
    while ($row = mysql_fetch_assoc($res))
    {
      print "<tr id='perso_".$row['id']."'>\n";
      print "<td class='nom_perso'>".$row['nom']." ".$row['prenom'];
      print "</td>\n";
      // print "<td><a href=''><img src='img/modify.png'></a></td>\n";
      print "<td><a href='perso.php?action=modif&format=public' class='lien_modif'><img src='img/gtk-edit.png' alt='Modifier' title='Modifier'></a>";
      // On va mettre des div invisibles contenant les informations détaillées sur la personne
      print "<div id='infos_cachees_".$row['id']."' class='infos_caches'>";
      print "<span class='nom'>".$row["nom"]."</span>\n";
      print "<span class='prenom'>".$row["prenom"]."</span>\n";
      print "<span class='sections'>".$row["sections"]."</span>\n";
      print "<span class='section_principale'>".$row["section_principale"]."</span>\n";
      # On va gérer les groupes de la personne
      $res_groupes = SQL("select * from perso_groupe where id_perso = ".$row["id"]);
      $groupes = "";
      while ($row_groupe = mysql_fetch_assoc($res_groupes))
      {
        $groupes .= $row_groupe["id_groupe"].",";
      }
      print "<span class='groupes'>".$groupes."</span>\n";
      print "</div>";
      print "</td>\n";
      print "<td><a href='perso.php?action=agenda&format=public&id=".$row['id']."'><img src='img/agenda.png' alt='Présence' title='Présence'></a></td>\n";
      print "<td><a href='perso.php?action=liste_conges&format=public&id=".$row['id']."'><img src='img/weather-clear.png' alt='Gérer les absences' title='Gérer les absences'></a></td>\n";
//      print "<td><a href='perso.php?action=stat&format=public' class='lien_stats'><img src='img/ktimer.png' alt='Statistiques de cette personne' title='Statistiques de cette personne'></a></td>\n";
      print "<td><a href='perso.php?action=suppr&format=public' class='lien_suppr'><img src='img/cancel.png' alt='Supprimer' title='Supprimer'></a></td>\n";
      print "</tr>\n";
    }
    print "</table>";
    if (isset($_GET["post_message"]))
    {
      $post_message  = $_GET['post_message'];
    }
    else
    {
      $post_message = "";
    }

    if ($post_message)
    {
      if ($post_message == "creation")
      {
        print "<div id='post_message'>Création effectuée avec succés</div>\n";
      }
      elseif ($post_message == "suppression")
      {
        print "<div id='post_message'>Suppression effectuée avec succés</div>\n";
      }
      elseif ($post_message == "modification")
      {
        print "<div id='post_message'>Modification effectuée avec succés</div>\n";
      }
    }
    print "<div id='modif_perso'>\n";
    print "<input type='hidden' name='id' id='form_id' value=''/>";
    print "<input type='hidden' name='action' id='form_action' value=''/>";
    print "<form class='monForm'>\n";
    print "<fieldset>\n";
    print "\t<legend id='legend_form'>&nbsp;</legend>\n";
    print "<p>\n";
    print '<label for="form_nom">Nom : </label>'."\n";
    print '<input type="text" id="form_nom" name="nom"/>'."\n";
    print "</p>\n";
    print "<p>\n";
    print '<label for="form_prenom">Prénom : </label>'."\n";
    print '<input type="text" id="form_prenom" name="prenom"/>'."\n";
    print "</p>\n";
    print "<p>\n";
/*    print '<label for="form_section_ppale">Sect. principale : </label>'."\n";
    print "<select id='form_section_principale'>\n";
    print "<option value=''>&nbsp;</option>\n";
    $res = SQL("select * from sections order by ordre;");
    while ($row = mysql_fetch_assoc($res))
    {
      print "<option value='".$row["id"]."'>".$row["nom"]."</option>\n";
    }
    print "</select>\n";
    print "</p>\n"; */
    // On va faire la même pour les sections possibles
/*    print "<label>Sect. possibles : </label>\n";
    $res = SQL("select * from sections order by ordre;");
    print "<div id='liste_sections'>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      print "<input type='checkbox' class='chk_sect' id='sect_chk_".$row["id"]."' value='".$row['id']."'>".$row["nom"]."<br/>\n";
    }
    print "</div> <!-- Liste sections -->\n";
    print "<hr/>"; */
    print "<label>Groupes : </label>\n";
    $res = SQL("select * from groupes order by id;");
    print "<div id='liste_groupes'>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      print "<input type='checkbox' class='chk_gpe' id='gpe_chk_".$row["id"]."' value='".$row['id']."'>".$row["lib"]."<br/>\n";
    }
    print "</div> <!-- Liste sections -->\n";
    
    print "<br/>";
    print "<center>";
    print "<input type='submit' value='valider' id='valid_modif'/>";
    print "<br/>&nbsp;<span id='msg_modif'>&nbsp</span>";
    print "</center>";
    
    print "</fieldset>";
    print "</form>\n";
    print "</div>\n";
  }
  elseif ($action == "liste_conges")
  {
    $id = $_GET["id"];
    // On va récupérer les informations sur la personne concernée
    $req = "select nom, prenom from personnel where id = $id";
    $res = SQL($req);
    if (mysql_numrows($res) != 1)
    {
      print "<span class='erreur'>Erreur dans l'interrogation de la base ($req)</span>";
      exit;
    }
    
    $row = mysql_fetch_assoc($res);
    $nom    = $row["nom"];
    $prenom = $row["prenom"];
    
    print "<h1>Gestion des absences de <b>$prenom $nom</b></h1>";
    
    // On va gérer le post_message si jamais on vient de faire une opération
    if (isset($_GET['post_message']))
    {
      $post_message = $_GET['post_message'];
    }
    else
    {
      $post_message = "";
    }
    
    if ($post_message == "suppression")
    {
      print "<div id='post_message'>Suppression effectuée avec succès</div>";
    }
    elseif ($post_message == "creation")
    {
      print "<div id='post_message'>Création effectuée avec succès</div>";
    }
    elseif ($post_message == "modification")
    {
      print "<div id='post_message'>Modification effectuée avec succès</div>";
    }
    
    /********************/
    /* CONGÉS PERMANENT */
    /********************/
/*    print "<h2>Congés permanents <a href='pe_conges_perm.php?action=creation&id_perso=".$id."' id='a_add_conges_perm'><img src='img/add.png' alt='Ajouter un congés permanent' title='Ajouter un congés permanent' width='20px'/></a></h2>";
    print "<div id='gest_conges_perm'>";
    print "<form id ='form_conges_perm' class='monForm'>\n";
    print "<input type='hidden' name='gest_conges_perm_id_conges' id='gest_conges_perm_id_conges' value=''/>";
    print "<input type='hidden' name='id_perso' id='gest_conges_perm_id_perso' value='".$_GET['id']."'/>";
    print "<input type='hidden' name='action' id='gest_conges_perm_action' value=''/>";
    
    print "<fieldset>\n";
    print "\t<legend id='gest_conges_perm_titre_form'>Créer un congés</legend>\n";
    print "<p>\n";
    print '<label for="gest_conges_perm_jour">Jour : </label>'."\n";
    print "<select name='gest_conges_perm_jour' id='gest_conges_perm_jour'>\n";
    print "<option value=''>&nbsp;</option>\n";
    print "<option value='Lundi'>Lundi</option>\n";
    print "<option value='Mardi'>Mardi</option>\n";
    print "<option value='Mercredi'>Mercredi</option>\n";
    print "<option value='Jeudi'>Jeudi</option>\n";
    print "<option value='Vendredi'>Vendredi</option>\n";
    print "<option value='Samedi'>Samedi</option>\n";
    print "</select>";
    print "</p>\n";
    print "<p>\n";
    print '<label for="gest_conges_perm_horaire">Horaire : </label>'."\n";
    print "<select name='gest_conges_perm_horaire' id='gest_conges_perm_horaire'>";
    print "<option value=''>&nbsp;</option>\n";
    $res = SQL("select * from horaires order by id");
    while ($row = mysql_fetch_assoc($res))
    {
      print "<option value='".$row["id"]."'>".$row["heures"]."</option>\n";
    }
    print "</select>\n";
    print "</p>\n";
    print "<input type='submit' value='Valider' id='gest_conges_perm_valid_modif'/>\n";
    print "</div> <!-- fin #gest_conges_perm -->\n";
    print "</fieldset></form>\n";
    $res = SQL("select * from conges_permanent where id_perso = $id");
    if (mysql_numrows($res) > 0)
    {
      print "<table id='tab_conges_perm' class='liste_conges'>\n";
      print "<tr><th>Jour</th><th>Tranche</th><th>&nbsp;</th><th>&nbsp;</th>\n";
      while ($row = mysql_fetch_assoc($res))
      {
        print "<tr id='conges_perm_".$row["id"]."'>";
        print "<td>".$row["jour"]."</td>\n";
        print "<td class='tranche_".$row["tranche"]."'>".get_lib_tranche($row["tranche"])."</td>\n";
        print "<td><a href='pe_conges_perm.php?action=suppr&id_conges=".$row["id"]."&id_perso=".$row["id_perso"]."' class='confirm_necessaire'><img src='img/cancel.png' alt='Supprimer' title='Supprimer'></a></td>\n";  
        print "<td><a href='pe_conges_perm.php?action=modif&format=public&jour=".$row["jour"]."&tranche=".$row["tranche"]."&id_perso=".$row["id_perso"]."' class='a_modif_conges_perm'><img src='img/gtk-edit.png' alt='Modifier' title='Modifier'></a></td>";
        print "</tr>\n";
      }
      print "</table>";
    }

    print "<br style='clear:both'/>";
                    */    
    /********************/
    /* CONGÉS PONCTUELS */
    /********************/
    print "<h2>Absences ponctuelles <a href='pe_conges.php?action=creation&id_perso=".$id."' id='a_add_conges'><img src='img/add.png' alt='Ajouter une absence' title='Ajouter une absence' width='20px'/></a></h2>";
    print "<div id='gest_conges'>\n";
    print "<form id ='form_conges' class='monForm'>\n";
    print "<input type='hidden' id='gest_conges_action'>\n";
    print "<input type='hidden' id='gest_conges_id_perso' value='".$_GET["id"]."'>\n";
    print "<input type='hidden' id='gest_conges_id_conges' value=''>\n";
    print "<fieldset>\n";
    print "<legend id='gest_conges_perm_titre_form'>Créer une absence</legend>\n";
    print "<p>\n";
    print '<label for="gest_conges_date_deb">Début : </label>'."\n";
    print "<input type='text' id='gest_conges_date_deb' class='small'/>";
    liste_heures("gest_conges_periode_deb");
    print "</p>\n";
    print "<p>\n";
    print '<label for="gest_conges_date_fin">Fin : </label>'."\n";
    print "<input type='text' id='gest_conges_date_fin' class='small'/>";
    liste_heures("gest_conges_periode_fin");
    print "</p>\n";
    print "<input type='submit' value='Valider' id='gest_conges_valid'/>\n";
    print "</fieldset></form>\n";
    print "</div> <!-- #gest_conges -->\n";    
    $res = SQL("select * from conges where id_perso = $id order by date_depart desc");
    if (mysql_numrows($res) > 0)
    {
      print "<table id='tab_conges_perm' class='liste_conges'>\n";
      print "<tr><th>Début</th><th>Fin</th><th>&nbsp;</th><th>&nbsp;</th>\n";
      while ($row = mysql_fetch_assoc($res))
      {
        print "<tr id='conges_".$row["id"]."'>";
        $date_depart  = date_us_to_fr($row["date_depart"]);
        $date_fin     = date_us_to_fr($row["date_fin"]);
        
        print "<td><span class='date'>".$date_depart."</span><br/><span class='periode'>".$row["dm_depart"]."</span></td>\n";
        print "<td><span class='date'>".$date_fin."</span><br/><span class='periode'>".$row["dm_fin"]."</span></td>\n";
        print "<td><a href='pe_conges.php?action=suppr&id_conges=".$row['id']."&id_perso=".$id."' class='confirm_necessaire'><img src='img/cancel.png' alt='Supprimer' title='Supprimer'></a></td>\n";  
        print "<td><a href='pe_conges.php' class='a_modif_conges'><img src='img/gtk-edit.png' alt='Modifier' title='Modifier'></a></td>";
        print "</tr>\n";
      }
      print "</table>";
    } 
  }
  elseif ($action == "agenda")
  {
    $id = $_GET["id"];
    if (isset($_POST['form_ok']))
    {
      print "<span style='font-weight:bold; color:green'>Mise à jour effectuée avec succès</span><br/>";
      // On va faire les mises à jour pour tous les jours de la semaine
      $semaine = Array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
      foreach ($semaine as $jour)
      {
        $res = SQL("select * from presence where id_perso = $id and jour = '$jour'");
        
        if (mysql_numrows($res) == 0)
        {
          // Mise à jour
          SQL("insert into presence (`id_perso`, `jour`, `debut`, `fin`) values ('$id', '$jour', '".$_POST['hidden-debut-slider-'.$jour]."', '".$_POST['hidden-fin-slider-'.$jour]."')");
        }
        else
        {
          SQL("update presence set debut='".$_POST['hidden-debut-slider-'.$jour]."', fin='".$_POST['hidden-fin-slider-'.$jour]."' where id_perso=$id and jour='$jour';");
        }
      }
    }
  ?>
    <form method="post" action="perso.php?id=<?php echo $id; ?>">
    <input type='hidden' name='action' value='agenda'/>
    <input type='hidden' name='form_ok' id='form_ok' value='1'/>
    <h2>Semaine type</h2>
    <h3>Lundi</h3> <?php init_zone_temps("Lundi"); ?>
    <div id="slider-Lundi" class="slider"></div>
    <h3>Mardi</h3> <?php init_zone_temps("Mardi"); ?>
    <div id="slider-Mardi" class="slider"></div>
    <h3>Mecredi</h3> <?php init_zone_temps("Mercredi"); ?>
    <div id="slider-Mercredi" class="slider"></div>
    <h3>Jeudi</h3> <?php init_zone_temps("Jeudi"); ?>
    <div id="slider-Jeudi" class="slider"></div>
    <h3>Vendredi</h3> <?php init_zone_temps("Vendredi"); ?>
    <div id="slider-Vendredi" class="slider"></div>
    <h3>Samedi</h3> <?php init_zone_temps("Samedi"); ?>
    <div id="slider-Samedi" class="slider"></div>
    
    <script type="text/javascript">


      <?php
      // On va initialiser les différentes zones avec ce qu'on trouve dans la base
      // On interrroge 6 fois la base, il aurait mieux valu tout initialiser à une valeur par défaut et changer
      // ensuite via Jquery le min et max de chacun des sliders mais ça ne semble pas fonctionner (SMA, 06/2010)
      $semaine = Array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
      foreach ($semaine as $jour)
      {
        
        $res = SQL("select * from presence where id_perso = $id and jour = '$jour'");
        if (mysql_numrows($res) == 1)
        {
          
          $row = mysql_fetch_assoc($res);
          $jour = $row["jour"];
          $deb = $row["debut"];
          $fin = $row["fin"];
          
          // On doit calculer le nombre de minutes pour $deb et $fin
          $deb_minutes = ( substr($deb, 0, 2) * 60 ) + (substr($deb, 3, 2));
          $fin_minutes = ( substr($fin, 0, 2) * 60 ) + (substr($fin, 3, 2));
          
          print "$(\"#debut-slider-".$jour."\").text(\"".$deb."\");\n";
          print "$(\"#fin-slider-".$jour."\").text(\"".$fin."\");\n";
          print "$(\"#hidden-debut-slider-".$jour."\").attr('value', \"".$deb."\");\n";
          print "$(\"#hidden-fin-slider-".$jour."\").attr('value', \"".$fin."\");\n";
          
          print '$("#slider-'.$jour.'").slider({'."\n";
          print "   range: true,\n";
          print "   step:15,\n";
          print "   min: 480,\n";
          print "   max: 1200,\n";
          print "   values: [".$deb_minutes.", ".$fin_minutes."],\n";
          print "   slide: slideTime\n";
          print "   });\n";
        }
        else
        {
          print '$("#slider-'.$jour.'").slider({'."\n";
          print "   range: true,\n";
          print "   step:15,\n";
          print "   min: 480,\n";
          print "   max: 1200,\n";
          print "   values: [480, 1200],\n";
          print "   slide: slideTime\n";
          print "   });\n";
        }
      }
      ?>
      
			function slideTime(event, ui)
      {
				var minutes0 = parseInt($(this).slider("values", 0) % 60);
				var hours0 = parseInt($(this).slider("values", 0) / 60 % 24);
				var minutes1 = parseInt($(this).slider("values", 1) % 60);
				var hours1 = parseInt($(this).slider("values", 1) / 60 % 24);
        
				$("#debut-" + $(this).attr("id")).text(getTime(hours0, minutes0));
        $("#fin-" + $(this).attr("id")).text(getTime(hours1, minutes1));
        $("#hidden-debut-" + $(this).attr("id")).attr('value', getTime(hours0, minutes0));
        $("#hidden-fin-" + $(this).attr("id")).attr('value', getTime(hours1, minutes1));
			}
      
			function getTime(hours, minutes) {
				var time = null;
				minutes = minutes + "";
        hours = hours + "";
        
				if (minutes.length == 1) {
					minutes = "0" + minutes;
				}
        if (hours.length == 1) {
					hours = "0" + hours;
				}
        
				return hours + ":" + minutes;
			}
    </script>
    <input type='submit' value="Enregistrer"/>
  </form> 
  <?php
  
  }
  print "</div>";
  include("include/bas.php");
  
  function date_us_to_fr($in)
  {
    $out = substr($in, 8, 2)."/".substr($in, 5, 2)."/".substr($in, 0, 4);
    return $out;
  }
  
  // Cette fonction va générer le select de création des heures.
  function liste_heures($id, $select = "")
  {
    print "<select class='small' id='$id'>\n";
    for ($i = 8; $i <= 20; $i++)
    {
      $heure = sprintf("%02d", $i);
      print "<option value='$heure:00'";
      if ($select == "$heure:00")
      {
        print " selected";
      }
      print ">$heure:00</option>\n";
      if ($i != 20)
      {
        print "<option value='$heure:30'";
        if ($select == "$heure:30")
        {
          print " selected";
        }      
        print ">$heure:30</option>\n";
      }
    }
    print "</select>\n";
  }
  
  function init_zone_temps($jour)
  {
    print '<input type="hidden" id="hidden-debut-slider-'.$jour.'" name="hidden-debut-slider-'.$jour.'" value="08:00"/>';
    print '<input type="hidden" id="hidden-fin-slider-'.$jour.'" name="hidden-fin-slider-'.$jour.'" value="20:00"/>';
    print '<span id="debut-slider-'.$jour.'">08:00</span> - <span id="fin-slider-'.$jour.'">20:00</span><br/>';
  }
?>