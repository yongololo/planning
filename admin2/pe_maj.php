<?php
  $action = $_GET["action"];
  include("include/connect.php");
  include("include/utils.php");
  
  if ($action == "modification")
  {
    // On doit mettre à jour la personne
    $id                 = $_GET["id"];
    $nom                = $_GET["nom"];
    $prenom             = $_GET["prenom"];
    $sections           = $_GET["sections"];
    $section_principale = $_GET["section_principale"];


    $sql = "update personnel set nom='$nom', prenom='$prenom' where id=$id;";
    $result = mysql_query($sql);
    if (!$result) {
      echo "Impossible d'exécuter la requête ($sql) dans la base : " . mysql_error();
    }
    
    # On va ensuite gérer les groupes pour lesquels on doit mettre en place
    # un système avec une table intermédiaire : personnel <-> perso_groupe <-> groupes
    # On commence par supprimer tous les groupes :
    SQL("delete from perso_groupe where id_perso = $id");
    $groupes            = $_GET['groupes'];
    $tab_gpe = preg_split("/,/", $groupes);
    foreach ($tab_gpe as $un_groupe)
    {
      if ($un_groupe != "")
      {
        # On va l'insérer
        SQL("insert into perso_groupe (`id_perso`, `id_groupe`) values ('$id', '$un_groupe');");
      }
    }
    
    print '1';
  }
  elseif ($action == "creation")
  {
    $nom                = $_GET["nom"];
    $prenom             = $_GET["prenom"];
    $sections           = $_GET["sections"];
    $section_principale = $_GET["section_principale"];
    $sql = "insert into personnel (`nom`, `prenom`, `sections`, `section_principale`) values ('$nom', '$prenom', '$sections', '$section_principale')";
    $result = mysql_query($sql);
    $id = mysql_insert_id();
    $groupes            = $_GET['groupes'];
    $tab_gpe = preg_split("/,/", $groupes);
    foreach ($tab_gpe as $un_groupe)
    {
      if ($un_groupe != "")
      {
        # On va l'insérer
        SQL("insert into perso_groupe (`id_perso`, `id_groupe`) values ('$id', '$un_groupe');");
      }
    }
    
    if (!$result) {
      echo "Impossible d'exécuter la requête ($sql) dans la base : " . mysql_error();
    }
    else
    {
      print '1';
    }
    
  }
  elseif ($action == "suppression")
  {
    $id                 = $_GET["id"];
    $sql = "update personnel set actif='0' where id=$id;";
    $result = mysql_query($sql);
    if (!$result) {
      echo "Impossible d'exécuter la requête ($sql) dans la base : " . mysql_error();
    }
    else
    {
      print '1';
    }
  }
?>