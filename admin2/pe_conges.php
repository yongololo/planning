<?php
  include("include/connect.php");
  include("include/utils.php");
  
  if (isset($_GET["action"]))
  {
    $action = $_GET["action"];
  }
  else
  {
    $action = "";
  }
  
  if (isset($_GET["id_perso"]))
  {
    $id_perso = $_GET["id_perso"];
  }
  else
  {
    $id_perso = "";
  }
  
  if (isset($_GET["id_conges"]))
  {
    $id_conges = $_GET["id_conges"];
  }
  else
  {
    $id_conges = "";
  }
  
  if (isset($_GET["date_deb"]))
  {
    $date_deb = formate_date($_GET["date_deb"]);
  }
  else
  {
    $date_deb = "";
  }
  
  if (isset($_GET["date_fin"]))
  {
    $date_fin = formate_date($_GET["date_fin"]);
  }
  else
  {
    $date_fin = "";
  }

  if (isset($_GET["periode_deb"]))
  {
    $periode_deb = $_GET["periode_deb"];
  }
  else
  {
    $periode_deb = "";
  }

  if (isset($_GET["periode_fin"]))
  {
    $periode_fin = $_GET["periode_fin"];
  }
  else
  {
    $periode_fin = "";
  }

  if (isset($_GET["type_conges"]))
  {
    $type_conges  = $_GET["type_conges"];
  }
  else
  {
    $type_conges = "";
  }
  
  
  if ($action == "suppr")
  {
    # On va effectuer la suppression de la tranche et on
    $res = SQL("delete from conges where id = $id_conges");
    header('Location: perso.php?action=liste_conges&id='.$id_perso.'&post_message=suppression');
  }
  elseif ($action == "creation")
  {
    $res = SQL("insert into conges (`id_perso`, `date_depart`, `dm_depart`, `date_fin`, `dm_fin`, `type`) values ($id_perso, '$date_deb', '$periode_deb', '$date_fin', '$periode_fin', '$type_conges')");
    print "1";
  }
  elseif ($action == "modification")
  {
    $res = SQL("update conges set date_depart='$date_deb', dm_depart='$periode_deb', date_fin='$date_fin', dm_fin='$periode_fin', type='$type_conges' where id=$id_conges");
    print "1";
  }
  
  function formate_date($in)
  {
    $out = substr($in, 6, 4)."-".substr($in, 3, 2)."-".substr($in, 0, 2);
    return $out;
  }
?>