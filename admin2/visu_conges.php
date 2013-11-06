<?php
  include("include/connect.php");
  include("include/utils.php");
  
  $stamp_modif  = $_GET["stamp_modif"];
  $annee        = $_GET["annee"];
  $semaine      = $_GET["semaine"];
  
  $date_jour = date("d/m/Y", $stamp_modif);
  include("include/haut.php");
  print "<h1>Congés pour le ".$date_jour."</h1>";
  print "<a href='planning.php?annee=$annee&semaine=$semaine&action=modification&stamp_modif=$stamp_modif'>Revenir au planning</a>";
  print "<br style='clear:both'/>";
  // On doit trouver les congés pour la journée
  print "<h2>Personnes en congés</h2>";
  $dateSQL = date("Y-m-d", $stamp_modif);
  $res = SQL("select * from conges, personnel where conges.id_perso = personnel.id and date_depart <= '$dateSQL' and date_fin >= '$dateSQL';");
  if (mysql_numrows($res) > 0)
  {
    print "<ul>\n";
    while ($row = mysql_fetch_assoc($res))
    {
      print "<li><b>".$row["prenom"]." ".$row["nom"]."</b> (du ".date_en_to_fr($row["date_depart"])." ".$row["dm_depart"]." au ".date_en_to_fr($row["date_fin"])." ".$row["dm_fin"].")</li>";
    }
    print "</ul>\n";
  }
  else
  {
    print "<i>Pas de congés à cette date</i>\n";
  }
  include("include/bas.php");
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