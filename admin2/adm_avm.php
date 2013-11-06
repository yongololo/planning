<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>
    Gestion de planning - SCD Bordeaux 3
  </title>
  <!-- La feuille de styles "base.css" doit être appelée en premier. -->
  <link rel="stylesheet" type="text/css" href="css/general.css" media="all" />
  
  <!-- On va gérer les inclusions javascript -->
  <script type='text/javascript' src='js/jquery-1.4.2.min.js'></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js" type="text/javascript"></script>
  <script type='text/javascript' src="js/jquery.ui.datepicker-fr.js"></script>
  <script type='text/javascript'>
  $(document).ready(function() {
    $("#input_date").datepicker();
  });
  </script>
  <link href="css/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" type="text/css"/>
  <style>
  </style>
</head>
<?php
  $interne = "";
  include("include/connect.php");
  include("include/utils.php");
  
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
    $action = "consult";
  }
  
  if (isset($_GET["annee"]))
  {
    $annee = $_GET["annee"];
  }
  elseif (isset($_POST["annee"]))
  {
    $annee = $_POST["annee"];
  }
  
  if (isset($_GET["mois"]))
  {
    $mois = $_GET["mois"];
  }
  elseif (isset($_POST["mois"]))
  {
    $mois = $_POST["mois"];
  }
  
  if (isset($_GET["jour"]))  { $jour = $_GET["jour"];  }
  
  if ( (isset($annee)) && (isset($mois)) && (isset($jour)) )
  {
    $date_preset = "$jour/$mois/$annee";
  }

  # Si on est sur une modification on va aller récupérer les
  # informations dans la base de données
  if ($action == "modification")
  {
    $id_orig = $_GET["id"];
    $res = SQL("select * from avm where id = $id_orig");
    if (mysql_numrows($res) == 1)
    {
      $row = mysql_fetch_assoc($res);
      $date_preset = $row["date"];
      $date_preset = substr($date_preset, 8, 2)."/".substr($date_preset, 5, 2)."/".substr($date_preset, 0, 4);
      
      $h_deb_preset = $row["h_deb"];
      $h_fin_preset = $row["h_fin"];
      
      $notes_preset = $row["notes"];
      
      $mois = substr($row["date"], 5, 2);
      $annee = substr($row["date"], 0, 4);
      
      $interne = $row["interne"];
    }
    else
    {
      print "ERREUR d'id";
      die;
    }
  }
  
  if ($action == "modification_ok")
  {
    $id_orig = $_POST["id_orig"];
  }
  
  
  // On va récupérer les valeurs à modifier
?>
<body id='page_<?php echo $action; ?>'>
<?php
  if ( ($action == "creation") || ($action == "modification") )
  {
    // On va afficher le formulaire
    print "<form method='post' action='adm_avm.php'>\n";
    if ($action == "creation")
    {
      print "<input type='hidden' name='action' value='creation_ok'/>\n";  
    }
    elseif ($action == "modification")
    {
      print "<input type='hidden' name='action' value='modification_ok'/>\n";
      print "<input type='hidden' name='id_orig' value='$id_orig'/>\n";
    }
    
    print "<input type='hidden' name='annee' value='$annee'/>\n";
    print "<input type='hidden' name='mois' value='$mois'/>\n";
    
    
    print "<input type='text' id='input_date' name='input_date'";
    if (isset($date_preset))
    {
      print " value='$date_preset'";
    }
    print "/>";
    
    if (isset($h_deb_preset))
    {
      liste_heures("h_deb", $h_deb_preset);
    }
    else
    {
      liste_heures("h_deb", "08:00");
    }

    if (isset($h_fin_preset))
    {
      liste_heures("h_fin", $h_fin_preset);
    }
    else
    {
      liste_heures("h_fin", "20:00");
    }
    
    
    print "<br/>";
    print "<textarea name='infos' id='infos' rows='6' cols='50'>";
    if (isset($notes_preset))
    {
      print $notes_preset;
    }
    print "</textarea><br/>";
    
    print "<small>Usage interne SCD [pour statistiques] : </small><input type='checkbox' name='interne' value='1'";
    if ($interne)
    {
      print " checked='checked'";
    }
    print "/><br/>";
    
    if ($action == "creation")
    {
      print "<input type='submit' value='Ajouter'/>";      
    }
    elseif ($action == "modification")
    {
      print "<input type='submit' value='Modifier'/>";      
    }
    print "</form>\n";
  }
  elseif ( ($action == "creation_ok") || ($action == "modification_ok") )
  {
    // On va enregistrer ça dans la base de données
    $date   = $_POST["input_date"];
    $date_formate = substr($date, 6, 4)."-".substr($date, 3, 2)."-".substr($date, 0, 2);
    $h_deb  = $_POST["h_deb"];
    $h_fin  = $_POST["h_fin"];
    $infos  = $_POST["infos"];
    if (isset($_POST["interne"]))
    {
      $interne  = $_POST["interne"];
    }
    else
    {
      $interne  = "0";
    }
    
    
    if ($action == "creation_ok")
    {
      $res = SQL("insert into avm (`date`, `h_deb`, `h_fin`, `notes`, `interne`) values ('$date_formate', '$h_deb', '$h_fin', '$infos', '$interne')");  
      print "Ajout effectué avec succès.";
    }
    elseif ($action == "modification_ok")
    {
      $res = SQL("update avm set `date` = '$date_formate', `h_deb` = '$h_deb', `h_fin` = '$h_fin', `notes` = '$infos', `interne`='$interne' where id=$id_orig");
      print "Modification effectuée avec succès";
    }
    print "<br/>\n";
    print "<a href='../avm.php?annee=$annee&mois=$mois'>Retour au planning</a>\n";
    
    
  }

  function liste_heures($id, $select = "")
  {
    print "<select class='small' name='$id' id='$id'>\n";
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
?>
</body>
</html>