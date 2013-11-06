<?php
  include("include/connect.php");
  include("include/utils.php");
  $action     = $_GET["action"];
  if (isset($_GET["jour"]))
  {
    $jour       = $_GET["jour"];    
  }
  if (isset($_GET["tranche"]))
  {
    $tranche    = $_GET["tranche"];    
  }
  $id_perso   = $_GET["id_perso"];
  $id_conges  = $_GET["id_conges"];

  if ($action == "suppr")
  {
    # On va effectuer la suppression de la tranche et on
    $res = SQL("delete from conges_permanent where id = $id_conges");
    header('Location: perso.php?action=liste_conges&id='.$id_perso.'&post_message=suppression');
  }
  elseif ($action == "creation")
  {
    $res = SQL("insert into conges_permanent (`id_perso`, `jour`, `tranche`) values ($id_perso, '$jour', $tranche)");
    print "1";
  }
  elseif ($action == "modification")
  {
    $res = SQL("update conges_permanent set id_perso = $id_perso, jour = '$jour', tranche=$tranche where id=$id_conges");
    print "1";
  }
?>