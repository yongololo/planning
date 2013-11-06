<?php
  include("include/connect.php");
  include("include/utils.php");
  
  // Ce script va fournir au format JSON la liste des personnes disponibles pour une plage horaire
  // et une section donnée
  
  $id_s     = $_GET["id_s"];
  $id_h     = $_GET["id_h"];
  $stamp    = $_GET["stamp"];
  $persos   = $_GET["persos"];
  
  if (isset($_GET["contenu"]))
  {
    $contenu  = $_GET["contenu"];
  }
  else
  {
    $contenu = "";
  }
    
  // Section spéciale en ce qui concerne les observations
  if ($id_s == "OBS")
  {
    $res = SQL("select * from Observations where date = '".date("Y-m-d", $stamp)."' and `Id-horaire` = $id_h;");
    if (mysql_numrows($res) == 0)
    {
      // On doit faire une insertion
      $req = "insert into Observations (`Date`, `Id-horaire`, `Observation`) values (";
      $req .= "'".date("Y-m-d", $stamp)."', $id_h, '".$contenu."')";
      SQL($req);
    }
    else
    {
      // On doit faire une mise à jour
      $row = mysql_fetch_array($res);
      $id = $row["id"];
      
      $req = "update Observations set Observation='".$contenu."' where id=$id;";
      SQL($req);
    }
  }
  else
  {
    // On va commencer par supprimer tous les éléments de cette case :
    $res = SQL("delete from planning where date = '".date("Y-m-d", $stamp)."' and id_section = $id_s and id_horaire = $id_h;");
    
    // Ensuite on va ajouter tous les collègues qui viennent d'être saisis dans cette case
    foreach ($persos as $pos => $id_p)
    {
      # On une position et un identifiant
      # On va vérifier si il existe déjà quelquechose
      # On doit gérer le numéro de semaine, à partie du stamp c'est possible.
      $num_semaine = date("W", $stamp);
      if ($id_p != "")
      {
        $req = "insert into planning (`num_semaine`, `date`, `id_section`, `id_perso`, `id_horaire`, `position`) values (";
        $req .= "'".$num_semaine."', '".date("Y-m-d", $stamp)."', $id_s, $id_p, $id_h, $pos)";
        SQL($req);      
      }  
      
      /*
      
      // Si on n'a pas de personnage, soit on souhaite supprimer la personne
      // soit on souhaite supprimer ce créneau
      if ($id_p == "")
      {
        $res = SQL("delete from planning where date = '".date("Y-m-d", $stamp)."' and id_section = $id_s and id_horaire = $id_h and position= $pos;");  
      }
      else
      {
        $res = SQL("select * from planning where date = '".date("Y-m-d", $stamp)."' and id_section = $id_s and id_horaire = $id_h and position= $pos;");
        if (mysql_numrows($res) == 0)
        {
          $req = "insert into planning (`num_semaine`, `date`, `id_section`, `id_perso`, `id_horaire`, `position`) values (";
          $req .= "'".$num_semaine."', '".date("Y-m-d", $stamp)."', $id_s, $id_p, $id_h, $pos)";
          SQL($req);
        }
        else
        {
          SQL("update planning set id_perso = '$id_p' where date = '".date("Y-m-d", $stamp)."' and id_section = $id_s and id_horaire = $id_h and position= $pos;");
        }
      } */
    }
  }
?>