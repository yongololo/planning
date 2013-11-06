<?php
  if (!file_exists("admin2/include/connect.php"))
  {
    print "Fichier admin2/include/connect.php absent ou mal configur&eacute;";
    exit;
  }
  include("admin2/include/connect.php");
  include("admin2/include/utils.php");
  if (isset($_GET["format"]))
  {
    $format = $_GET["format"];
  }
  else
  {
    $format = "";
  }
  
  if ($format != "pdf")
  {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Plannings BU de Lettres</title>
  <link rel="stylesheet" type="text/css" href="css/main.css" media="all" />
  <link rel="stylesheet" type="text/css" href="css/sections.css" media="all" />
  <script type='text/javascript' src='admin2/js/jquery-1.4.2.min.js'></script>
  <script type='text/javascript'>
    $(document).ready(function()
    {
      // On va faire en sorte que lorsque l'on parcourt une ligne,
      // le contenu des cases soit modifié
      $(".ligne_etage").hover(
        function(){
          // On remet à zéro
          var background_orig = $(this).css("background-color");
          $(this).css("color", background_orig);
          $(this).find("th").css("color", background_orig);
          $(this).css("background-color", "#333");
          $(this).find("th").css("background-color", "#333");
//          $(this).css("color", "white");
 //         $(this).css("font-weight", "bold");
        },
        // Le handleout
        function()
        {
          $(this).css("color", "");
          $(this).find("th").css("color", "");
          $(this).css("background-color", "");
          $(this).find("th").css("background-color", "");
        }
      );
    });
  </script>

</head>
<body>
  <a href='admin2/index.php' style='color:#FF9900; text-decoration:none; font-weight:bold;'><img style='border:0px' src='img/icon_key.gif' alt='connexion'/>&nbsp;Administration</a>

<?php
  }
  if (isset($_GET["auj"]))
  {
    $semaine = date("W");
  }
  elseif (isset($_GET["semaine"]))
  {
    $semaine = $_GET["semaine"];
  }
  else
  {
    $semaine = "";
  }
  
  if (isset($_GET["auj"]))
  {
    $annee = date("Y");
  }
  elseif (isset($_GET["annee"]))
  {
    $annee = $_GET["annee"];
  }
  else
  {
    $annee = "";
  }
  
  if (isset($_GET["auj"]))
  {
    $action = "consultation";
  }
  elseif (isset($_GET["action"]))
  {
    $action = $_GET["action"];
  }
  else
  {
    $action = "";
  }
  
  if (isset($_GET["auj"]))
  {
    $jour = traduit_code_jour(date("D"));
  }
  elseif (isset($_GET["jour"]))
  {
    $jour = $_GET["jour"];
  }
  else
  {
    $jour = "";
  }
  
  
  if (isset($_GET["stamp_modif"]))
  {
    $stamp_modif = $_GET["stamp_modif"];
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
  
  if ( ($semaine != "") && ($stamp_modif != "") )
  {
    // On a sélectionné la semaine mais le stamp_modif est faux
  }
  
  
  //###############################################################//
  // Affichage d'une année complète pour permettre la modification //
  //###############################################################//
  if ($semaine == "")
  {
    print "<h1><a href='index.php?annee=".($annee - 1)."'>&lt;&lt;</a> Plannings de $annee <a href='index.php?annee=".($annee + 1)."'>&gt;&gt;</a></h1>";
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
        print "<a href='index.php?annee=$annee&semaine=$i&action=consultation'>Consulter</a>";
      }
      else
      {
        print "\t\t<td class='semaine_inexist $classe_semaine_courante'>";
        print "s. $i (".date("d/m", $tmp[0])." - ".date("d/m", $tmp[sizeof($tmp) - 1]).")<br/>";
        print "<span class=''>Non créé</span>";
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
  //#################################################//
  // Affichage d'un jour seulement pour modification //
  //#################################################//
  elseif ($action == "consultation")
  {
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
    

    if (isset($_GET["auj"]))
    {
      // Si on veut afficher la date du jour on n'a forcément pas le
      // stamp_modif. On va le chercher à partir du nom du jour
      switch ($jour)
      {
        case "Lundi":
          $stamp_modif = $tab_semaine[0];
          break;
        case "Mardi":
          $stamp_modif = $tab_semaine[1];
          break;
        case "Mercredi":
          $stamp_modif = $tab_semaine[2];
          break;
        case "Jeudi":
          $stamp_modif = $tab_semaine[3];
          break;
        case "Vendredi":
          $stamp_modif = $tab_semaine[4];
          break;
        case "Samedi":
          $stamp_modif = $tab_semaine[5];
          break;
        default:
          $stamp_modif = $tab_semaine[0];
          break;
      }
    }
    elseif ($stamp_modif == "")
    {
      # On est en règle général sur la modification d'une semaine
      # le stamp_modif va donc correspondre au premier jour de la semaine
      $stamp_modif = $tab_semaine[0];
    }


    # Ici on a une semaine et on veut la modifier
    if ($format == "pdf")
    {
      define('FPDF_FONTPATH', 'admin2/fpdf/font/');
      require('admin2/fpdf/cellpdf.php');
      $pdf=new CellPDF('L','mm','A4');
      $pdf->AddPage();

      $jour = get_nom_jour_from_datestamp($stamp_modif);
      
      // Ajout du titre
      $pdf->SetFont('Arial','B',15);
      $pdf->SetTextColor(120, 120, 120);
      $pdf->Cell(80);
      $pdf->Cell(30, 10, "$jour ".date("d-m-Y", $stamp_modif));
      $pdf->Ln(20);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFont('Arial', '', 12);
    }
    else
    {
      print "<h1>";
      print "Planning du ".date("d/m/Y", $stamp_modif)." (sem. $semaine)";
      print "</h1>\n";
    }
    
    $jour_courant = get_nom_jour_from_datestamp($stamp_modif);
    
    if ($format != "pdf")
    {
      // On va stocker le stamp_modif dans un span caché pour
      // pouvoir l'utiliser plus facilement en javascript
      print "<span id='stamp_modif' title='" . $stamp_modif . "'>&nbsp;</span>";
      
      # On va ensuite afficher une liste de liens permettant d'accéder directement aux jours
      print "<div id='liste_jours'>\n";
      if ($semaine > 1)
      {
        print "<div class='un_jour'><a href='index.php?annee=$annee&semaine=".($semaine - 1)."&action=consultation'>[sem. précédente]</a></div>";
      }
      else
      {
        print "<div class='un_jour'><a href='index.php?annee=".($annee - 1)."&semaine=52&action=consultation'>[sem. précédente]</a></div>";
      }
      for ($i = 0; $i < sizeof($tab_semaine); $i++)
      {
        $datestamp = $tab_semaine[$i];
        $nom_jour = get_nom_jour_from_datestamp($datestamp);
        if ($datestamp == $stamp_modif)
        {
          print "<div class='un_jour'>$nom_jour</div>\n";
        }
        else
        {
          print "<div class='un_jour'><a href='index.php?annee=$annee&semaine=$semaine&action=consultation&stamp_modif=$datestamp'>$nom_jour</a></div>\n";
        }
      }
      if ($semaine < 52)
      {
        print "<div class='un_jour'><a href='index.php?annee=$annee&semaine=".($semaine + 1)."&action=consultation'>[sem. suivante]</a></div>";
      }
      else
      {
        print "<div class='un_jour'><a href='index.php?annee=".($annee + 1)."&semaine=1&action=consultation'>[sem. suivante]</a></div>";
      }
      print "</div> <!-- Fin liste_jours -->\n";
    }   
    
    # On va ensuite afficher la journée dans son intégralité en proposant sur chaque case
    # la possibilité de mofifier le contenu sans recharger (via AJAX)
    $liste_sections = SQL("select * FROM sections ORDER BY `ordre` ASC");
    $nb_sections    = mysql_num_rows($liste_sections);
    
    # SMA 201207 : On va commencer par regarder si l'on est pas sur une semaine d'été
    $res = SQL("select * from semaines_ete where annee = $annee and num_sem = $semaine");
    $horaire_ete = mysql_numrows($res);
    
    #$liste_horaires = SQL("SELECT * FROM horaires, jours_horaires WHERE jours_horaires.id_horaire = horaires.id and jours_horaires.jour = '$jour_courant'");
    if ($horaire_ete)
    {
      $liste_horaires = SQL("SELECT * FROM horaires WHERE id in (select distinct id_horaire_ete from jours_horaires where jour = '$jour_courant' and id_horaire_ete != 0)");  
    }
    else
    {
      $liste_horaires = SQL("SELECT * FROM horaires WHERE id in (select distinct id_horaire from jours_horaires where jour = '$jour_courant' and id_horaire != 0)");      
    }
    
    
    $nb_horaires    = mysql_num_rows($liste_horaires);
    
    # On va afficher le tableau
    if ($format != "pdf")
    {
      print "\n\n<table id='planning_jour'>\n";
    }
    
    $tab_horaires = array();
    
    # Affichage des en-têtes horaires
    if ($format != "pdf")
    {
      print "<thead><tr>\n";
      print "<th><span style='color:#000'><a style='color:#CC0000; font-size:0.8em; font-weight:normal' href='visu_conges.php?stamp_modif=".$stamp_modif."'>Congés du jour</a></span></th>\n"; 
    }
    else
    {
      case_pdf("");
    }
    # Case vide qui se place au dessus du nom des sections
    
    
    for ( $i_horaire = 0; $i_horaire < $nb_horaires; $i_horaire++)
    {
      $row_horaire = mysql_fetch_array($liste_horaires);
      if ($format != "pdf")
      {
        echo "<th class='entete_planning' id='col_".$row_horaire["id"]."'>".$row_horaire['heures']."</th>\n";        
      }
      else
      {
        case_pdf_centre_gras($row_horaire['heures']);
      }
      $tab_horaires[] = $row_horaire["id"];
    }
    
    if ($format != "pdf")
    {
      echo "</tr></thead>\n";
    }
    else
    {
      $pdf->Ln();
    }

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

      if ($format != "pdf")
      {
        print "<tr class='ligne_etage ".$s_class."'>\n";
        print "<th class='column1'>$s_nom</th>\n";
      }
      else
      {
        // On va initialiser la couleur pour la ligne en cours
        $fh = fopen("css/sections.css", "r");
        $couleur = "";
        while ($ligne = fgets($fh))
        {
          if ( (preg_match("/$s_class/", $ligne)) and ($couleur == "") )
          {
            while ($couleur == "")
            {
              $ligne = fgets($fh);
              if (preg_match("/rgb\((.*)\)/", $ligne, $match))
              {
                $couleur = $match[1];
              }
            }
          }
        }
        
        if ($couleur != "")
        {
          $tab_couleurs = preg_split("/,/", $couleur);
          $pdf->setFillColor($tab_couleurs[0], $tab_couleurs[1], $tab_couleurs[2]);
        }
        else
        {
          $pdf->setFillColor(255, 255, 255);
        }
        
        case_pdf_personnes($s_nom, $s_max_pers, 1);
      }
      
      # On va ensuite traverser les horaires
      foreach ($tab_horaires as $id_horaire)
      {
        # On donne à cette case un identifiant qui stocke l'identifiant
        if ($format != "pdf")
        {
          print "<td id='".$s_id."_".$id_horaire."'>";
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
        else
        {
          $contenu_cell = "";
          if ( (isset($occupation[$s_id])) && (isset($occupation[$s_id][$id_horaire])) )
          {
            foreach ($occupation[$s_id][$id_horaire] as $paire)
            {
              $contenu_cell .= $paire["nom"]."\n";
            }
          }
          case_pdf_personnes($contenu_cell, $s_max_pers, 1);
        }
      }
      
      if ($format !="pdf")
      {
        print "</tr>";
      }
      else
      {
        $pdf->Ln();
      }
    }
    


    // On va lire les observations pour ce jour là

    $req = "select * from Observations where date='".date("Y-m-d", $stamp_modif)."';";
    $res = SQL($req);
    $tab_obs = Array();
    while ($row = mysql_fetch_assoc($res))
    {
      $tab_obs[$row["Id-horaire"]] = $row["Observation"];
    }
    
    if ($format != "pdf")
    {
      // On ajoute une ligne vide
      print "<tr><td colspan='".(sizeof($tab_horaires) + 1)."'>&nbsp;</td></tr>\n";
      print "<tr style='font-size:0.8em'>\n";
      print "<td>Observations</td>";
    }
    else
    {
      $pdf->Ln();
    }

    
    foreach ($tab_horaires as $id_horaire)
    {
      if (isset($tab_obs[$id_horaire]))
      {
        if ($format != "pdf")
        {
          print "<td><span class='obs'>".$tab_obs[$id_horaire]."</span></td>";
        }
        else
        {
          case_pdf_personnes($tab_obs[$id_horaire], 3);
        }
      }
      else
      {
        if ($format != "pdf")
        {
          print "<td><span class='obs'>&nbsp;</span></td>";
        }
        else
        {
          case_pdf_personnes(" ", 3);
        }
      }
    }
    
    if ($format != "pdf")
    {
      print "</tr>";
      print "</table> <!-- #planning_jour -->\n";
      print "<div id='bandeau_bas'>\n";
      print "<a href='index.php?".$_SERVER['QUERY_STRING']."&format=pdf&stamp_modif=$stamp_modif'><img src='img/pdf.png' alt='PDF' title='PDF'/></a>";
      $query_string = $_SERVER['QUERY_STRING'];
      $query_string = str_replace("action=consultation", "action=modification", $query_string);
      print "<a href='admin2/auth.php?redir=".urlencode("planning.php?".$query_string)."'><img src='img/modif.png' alt='Modifier ce planning' title='Modifier ce planning'/></a>";
      print "</div>\n";
    }
    else
    {
        $pdf->Output();
    }
  }

  function case_pdf_centre_gras($texte, $largeur = 30, $hauteur = 10)
  {
    global $pdf;
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($largeur, $hauteur, utf8_decode($texte), 1, 0, "C");
    $pdf->SetFont('Arial', '', 12);
  }
    
  function case_pdf_centre($texte, $largeur = 30, $hauteur = 10)
  {
    global $pdf;
    $pdf->Cell($largeur, $hauteur, utf8_decode($texte), 1, 0, "C");
  }
  
  function case_pdf($texte, $largeur = 30, $hauteur = 12)
  {
    global $pdf;
    $texte = preg_replace("/\n$/", "", $texte);
    //$pdf->Cell($largeur, $hauteur, utf8_decode($texte), 1);
    $pdf->Cell($largeur, $hauteur, utf8_decode($texte), 1, 0, 'L');
  }
  
  function case_pdf_personnes($texte, $nb, $remplir = 0)
  {
    global $pdf;
    $texte = preg_replace("/\n$/", "", $texte);
    //$pdf->Cell($largeur, $hauteur, utf8_decode($texte), 1);
    $pdf->Cell(30, ($nb * 7), utf8_decode($texte), 1, 0, 'L', $remplir);
  }
?>
