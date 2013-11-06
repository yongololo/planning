<?php
  // Cette fonction lance une requête SQL et prend en charge
  // l'affichage des erreurs
  function SQL($req)
  {
    $result = mysql_query($req);
    if (!$result) {
      echo "Impossible d'exécuter la requête ($req) dans la base : " . mysql_error();
      exit;
    }
    return $result;
  }
  
  # inspiré de ce qu'on trouve dans les commentaires du post :
  # http://www.phpindex.com/index.php/2000/10/24/359-calcul-d-une-date-a-partir-du-numero-de-la-semaine
  function get_jours_semaines( $semaine, $annee )
  {
    $jour1erJanvierTS = mktime(0,0,0,1,1,$annee);
    if ( date("N",$jour1erJanvierTS) == 4 )
    {
      $jeudiSemaine1TS=$jour1erJanvierTS;
    }
    else
    {
      $jeudiSemaine1TS=strtotime("thursday",$jour1erJanvierTS);
    }
    
    $jeudiSemaineNTS = strtotime("+".($semaine-1)." weeks",$jeudiSemaine1TS);
    $lundi = strtotime("last monday",$jeudiSemaineNTS);
  
    $tab_sortie[] = $lundi;                 # Lundi 
    $tab_sortie[] = $lundi + (1*60*60*24);  # Mardi
    $tab_sortie[] = $lundi + (2*60*60*24);  # Mercredi
    $tab_sortie[] = $lundi + (3*60*60*24);  # Jeudi
    $tab_sortie[] = $lundi + (4*60*60*24);  # Vendredi
    $tab_sortie[] = $lundi + (5*60*60*24);  # Samedi
    
    return $tab_sortie;
  }//fin function
  
  function get_nom_jour_from_datestamp($datestamp)
  {
    $jour_eng = date("D", $datestamp);
    return traduit_code_jour($jour_eng);
  }
  
  function traduit_code_jour($code)
  {
    switch($code)
    {
      case "Mon" :
      case "1" :
        return "Lundi";
      case "Tue":
      case "2" :
        return "Mardi";
      case "Wed":
      case "3" :
        return "Mercredi";
      case "Thu":
      case "4" :
        return "Jeudi";
      case "Fri":
      case "5" :
        return "Vendredi";
      case "Sat":
      case "6" :
        return "Samedi";
      case "Sun":
      case "7" :
        return "Dimanche";
    }

    return "<span class='erreur'>Jour non traduit</span>";
  }

  function traduit_code_mois($code)
  {
    switch ($code)
    {
      case "01":
        return "Janvier";
      case "02":
        return "Février";
      case "03":
        return "Mars";
      case "04":
        return "Avril";
      case "05":
        return "Mai";
      case "06":
        return "Juin";
      case "07":
        return "Juillet";
      case "08":
        return "Août";
      case "09":
        return "Septembre";
      case "10":
        return "Octobre";
      case "11":
        return "Novembre";
      case "12":
        return "Décembre";
    }
  }

  function get_lib_tranche($tranche)
  {
    $res = SQL("select * from horaires where id=$tranche");
    if (mysql_numrows($res) == 1)
    {
      $row = mysql_fetch_assoc($res);
      return $row["heures"];
    }
    else
    {
      return "Libellé tranche non trouvé ($tranche)";
    }
  }
  
  function date_en_to_fr($in)
  {
    $out = substr($in, 8, 2)."/".substr($in, 5, 2)."/".substr($in, 0, 4);
    return $out;
  }
  
    function date_fr_to_en($in)
  {
    $sortie = substr($in, 6, 4)."-".substr($in, 3, 2)."-".substr($in, 0, 2);
    return $sortie;
  }

  
  function formate_heures($in)
  {
    $out = floor($in / 60)."h";
    $out = $out . sprintf("%02d", ($in % 60));
    return $out;
  }
?>