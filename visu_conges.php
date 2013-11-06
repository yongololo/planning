<?php
  include("admin2/include/connect.php");
  include("admin2/include/utils.php");
  
  $stamp_modif  = $_GET["stamp_modif"];
  
  $admin = "";
  if (isset($_GET['admin']))
  {
    $admin = 1;
    // On va gérer ici les sessions !
    session_start();
    if ((!isset($_SESSION['login'])) || (empty($_SESSION['login'])))
    {
      // la variable 'login' de session est non déclaré ou vide
      header("Location: auth.php");
    }
  }
  else
  {
    $admin = 0;
  }
  
  $date_jour = date("d/m/Y", $stamp_modif);
  $annee = date("Y", $stamp_modif);
  $semaine  = date("W", $stamp_modif);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>
    Gestion de planning - SCD Bordeaux 3
  </title>
</head>
<body>
<?php
  print "<h1>Congés pour le ".$date_jour."</h1>";
  if ($admin == 1)
  {
    print "<a href='admin2/planning.php?annee=$annee&semaine=$semaine&action=modification&stamp_modif=$stamp_modif'>Revenir au planning</a>";
  }
  elseif ($admin == 0)
  {
    print "<a href='index.php?annee=$annee&semaine=$semaine&action=consultation&stamp_modif=".$stamp_modif."'>Revenir au planning</a>";
  }
  print "<br style='clear:both'/>";
  // On doit trouver les congés pour la journée
  print "<h2>Personnes en congés</h2>";
  $dateSQL = date("Y-m-d", $stamp_modif);
  $res = SQL("select * from conges, personnel where conges.id_perso = personnel.id and date_depart <= '$dateSQL' and date_fin >= '$dateSQL' order by nom, prenom;");
  if (mysql_numrows($res) > 0)
  {
    print "<ul>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      print "<li><b>".$row["nom"]." ".$row["prenom"]."</b> (du ".date_en_to_fr($row["date_depart"])." ".$row["dm_depart"]." au ".date_en_to_fr($row["date_fin"])." ".$row["dm_fin"].")</li>";
    }
    print "</ul>\n";
  }
  else
  {
    print "<i>Pas de congés à cette date</i>\n";
  }

  exit;
  print "<h2>Congés permanents</h2>\n";
  $jour = traduit_code_jour(date("D", $stamp_modif));
  $res = SQL("select * from conges_permanent, personnel where conges_permanent.id_perso = personnel.id and jour='$jour' order by tranche;");
  $tranche_courante = "";
  if (mysql_numrows($res) > 0)
  {
    $num = 0;
    print "<ul>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      if ($tranche_courante != $row["tranche"])
      {
        // On doit ajouter un niveau
        if ($num != 0)
        {
          print "</li></ul>\n";
        }
        print "<li>".get_lib_tranche($row["tranche"])."<br/>";
        print "<ul>\n";
        $tranche_courante = $row["tranche"];
      }
      
      print "<li>".$row["prenom"]." ".$row["nom"]."</li>\n";
      
      $num++;
    }
  }
  else
  {
    print "<i>Aucun congés permanent ne s'applique à cette date</i>";
  }
?>