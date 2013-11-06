<?php
  include("include/connect.php");
  include("include/utils.php");
  
  // Ce script va fournir au format JSON la liste des personnes disponibles pour une plage horaire
  // et une section donnée
  $id_s   = $_GET["id_s"];
  $id_h   = $_GET["id_h"];
  $stamp  = $_GET["stamp"];
  $nom_jour = traduit_code_jour(date("D", $stamp));
  $jour_sql = date("Y-m-d", $stamp);
  
  // On va récupérer les informations sur l'horaire pour connaître le début et la fin
  $res = SQL("select * from horaires where id='$id_h'");
  $row = mysql_fetch_assoc($res);
  $row["heures"] = str_replace("h", ":", $row["heures"]);
  $tab_heures = preg_split("/ ?- ?/", $row["heures"]);
  $h_deb  = $jour_sql." ".$tab_heures[0];
  $h_fin  = $jour_sql." ".$tab_heures[1];
 
  // On doit commencer par récupérer le nombre de personnes max autorisées sur
  // la tranche
  $res = SQL("select max_pers from sections where id = $id_s");
  $nb_pers = mysql_fetch_row($res);
  $nb_pers = $nb_pers[0];

  // On doit aussi récupérer les personnes déjà en place pour pouvoir les préselectionner
  $persos_en_place = array();
  $req_en_place = "select * from planning where id_section = $id_s and id_horaire = $id_h and date = '" . date("Y-m-d", $stamp) ."';";
  $res = SQL($req_en_place);

  while ( $row = mysql_fetch_assoc($res) )
  {
    $persos_en_place[$row['position']] = $row["id_perso"];
  }

  $tab_sortie = array();
    
  for ($i = 0; $i < $nb_pers; $i++ )
  {
    $liste = array();
    $sql_conges = "and personnel.id not in (select id_perso from conges where ( ('$h_deb' >= concat(date_depart, ' ', dm_depart) ) and ('$h_deb' < concat(date_fin, ' ', dm_fin)) ) or ( ('$h_fin' >= concat(date_depart, ' ', dm_depart) ) and ('$h_fin' < concat(date_fin, ' ', dm_fin))))";
    // $sql_conges_perm = "and id not in (select id_perso from conges_permanent where jour = '$nom_jour' and tranche='$id_h')";
    $sql_conges_perm = "and personnel.id in (select id_perso from presence where debut <= '".$tab_heures[0]."' and fin >= '".$tab_heures[1]."' and jour='$nom_jour')";
    
    $liste_perso = SQL("select *, personnel.id as id_personne from personnel, perso_groupe, groupes where personnel.id = perso_groupe.id_perso and perso_groupe.id_groupe = groupes.id and actif='1' $sql_conges_perm $sql_conges order by prenom;");
  
    while ($row = mysql_fetch_assoc($liste_perso))
    {
      $id       = $row["id_personne"];
      $nom      = $row["nom"];
      $prenom   = $row["prenom"];
      $sections = $row["sect"];
      $tab_sections = preg_split("/,/", $sections);
      # Par défaut on va considérer qu'un usager n'est pas disponible
      
      $is_dispo = 0;
      
      foreach ($tab_sections as $section_possible)
      {
        if ($section_possible == $id_s)
        {
          $is_dispo = 1;
        }
      }
      
      if ($is_dispo)
      {
        $perso = array();
        $perso["id"]  = $id;
        $perso["nom"] = $prenom;
        if ( (isset($persos_en_place[$i])) && ($persos_en_place[$i] == $id) )
        {
          $perso["select"] = 1;
        }
        $liste[] = $perso;
      }
    }
    $tab_sortie[] = $liste;
  }
  
  print json_encode($tab_sortie);
?>