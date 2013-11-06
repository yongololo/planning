<?php
  // On est ici dans le fichier des stats au format PDF, on n'a pas besoin de proposer
  // la s?lection des dates, on se contente de r?cup?rer ce qui a ?t? demand? sur la page
  // et on l'exporte au PDF
  include("include/connect.php");
  include("include/utils.php");
  require_once("include/utils_stats.php");
  
  if (isset($_GET["type"]))  { $type = $_GET["type"]; }
  else  { $type = "";  }
  
  if (isset($_GET["annee"]))  { $annee = $_GET["annee"]; }
  else  { $annee = "";  }
  
  if (isset($_GET["mois"]))   { $mois = $_GET["mois"];  }
  else  { $mois = "";   }
  
  if (isset($_GET["semaine"]))  { $semaine = $_GET["semaine"];  }
  else  { $semaine = "";  }
  
  if (isset($_GET["jour_deb"])) { $jour_deb = $_GET["jour_deb"];  }
  else  { $jour_deb = ""; }

  if (isset($_GET["jour_fin"])) { $jour_fin = $_GET["jour_fin"];  }
  else  { $jour_fin = ""; }

  if (isset($_GET["intervalle_deb"]))  { $intervalle_deb = $_GET["intervalle_deb"]; }
  else  { $intervalle_deb = ""; }

  if (isset($_GET["intervalle_fin"]))  { $intervalle_fin = $_GET["intervalle_fin"]; }
  else  { $intervalle_fin = ""; }
  
  if (isset($_GET["code_zone"]))  { $code_zone = $_GET["code_zone"]; }
  else  { $code_zone = ""; }
    
  if (
    ( ($type == "annee") && ($annee != "") ) ||
    ( ($type == "semaine") && ($semaine != "") ) ||
    ( ($type == "mois") && ($mois != "") ) ||
    ( ($type == "periode") && ( ($intervalle_deb != "") || ($intervalle_fin != "") ) )
  )
  {
    $tab_groupe = Array();
    $paire = Array();
    $paire['lib'] = "Prêt, communication";
    $paire['code_zone'] = "tab_pretcomm";
    $paire['id']  = "2, 5, 9, 6";
    $tab_groupe[] = $paire;
    
    $paire = Array();
    $paire['lib'] = "Accueil";
    $paire['code_zone'] = "tab_acc";
    $paire['id']  = "7";
    $tab_groupe[] = $paire;
    
    $paire = Array();
    $paire['lib'] = "Renseignements";
    $paire['code_zone'] = "tab_rens";
    $paire['id']  = "1, 4, 8";
    $tab_groupe[] = $paire;
    
    $paire = Array();
    $paire['lib'] = "Tuteurs";
    $paire['code_zone'] = "tab_tuteur";
    $paire['id']  = "3, 10";
    $tab_groupe[] = $paire;
    
    require_once('fpdf/Table/class.fpdf_table.php');
    $titre_page = titre_page($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, "pdf");
    // On doit chercher le groupe pour le mettre dans l'en-t?te du fichier
    $lib_groupe = "";
    $id_sections = "";
    foreach ($tab_groupe as $paire)
    {
      if ($code_zone == $paire["code_zone"])
      {
        $lib_groupe   = $paire["lib"];
        $id_sections  = $paire["id"];
      }
    }

    require_once("fpdf/Table/header_footer.inc");
    require_once("fpdf/Table/table_def.inc");

    $pdf = new pdf_usage();        
    $pdf->Open();
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->AliasNbPages();      

    $columns = 2; //five columns

    $bg_color1 = array(234, 255, 218);
    $bg_color2 = array(165, 250, 220);
    $bg_color3 = array(255, 252, 249);

    $pdf->SetStyle("p","times","",10,"130,0,30");
    $pdf->SetStyle("t1","arial","",10,"0,151,200");
    $pdf->SetStyle("size","times","BI",13,"0,0,120");
    $pdf->SetStyle("titre", "arial", "", "16", "0,0,0");
    
    $pdf->MultiCellTag(100, 10, "<titre>Temps passé en service public</titre>");
    $tableau = temps_presence($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, $id_sections, "pdf");
    affiche_tableau_pdf($tableau);

#    $pdf->MultiCellTag(100, 10, "<titre>Permanences</titre>");
#    $tableau = permanences($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, $id_sections, "pdf");
#    affiche_tableau_pdf($tableau);

    $pdf->MultiCellTag(100, 10, "<titre>Samedi</titre>");
    $tableau = fermetures($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, 1, $id_sections, "pdf");
    affiche_tableau_pdf($tableau);
    
    $pdf->MultiCellTag(100, 10, "<titre>Fermetures</titre>");
    $tableau = fermetures($type, $annee, $semaine, $mois, $intervalle_deb, $intervalle_fin, 0, $id_sections, "pdf");
    affiche_tableau_pdf($tableau);
  }
  $pdf->Output();

  function affiche_tableau_pdf($tableau_src)
  {
    global $pdf;
    global $table_default_table_type;
    global $table_default_header_type;
    global $table_default_data_type;
    $columns = 2;
    
    $pdf->tbInitialize($columns, true, true);
    $pdf->tbSetTableType($table_default_table_type);
    for ($i=0; $i<$columns; $i++)
    {
      $header_type[$i] = $table_default_header_type;
    }
    
    $header_type[0]["TEXT"] = utf8_decode($tableau_src[0]["nom"]);
    $header_type[1]["TEXT"] = utf8_decode($tableau_src[0]["val"]);
    
    $aHeaderArray = array(
        $header_type
    );
    
    //set the Table Header
    $pdf->tbSetHeaderType($aHeaderArray, true); 
    $pdf->tbDrawHeader();     

    $data_type = Array();//reset the array
    for ($i=0; $i<$columns; $i++) $data_type[$i] = $table_default_data_type;
    $pdf->tbSetDataType($data_type);

    $fsize = 12;
    foreach (array_slice($tableau_src, 1)  as $paire)
    {
      $data = Array();
      $data[0]['TEXT'] = utf8_decode($paire["nom"]);
      $data[0]['T_SIZE'] = $fsize;
      $data[1]['TEXT'] = $paire["val"];
      $data[1]['T_SIZE'] = $fsize;
      $pdf->tbDrawData($data);
    }
        //output the table data to the pdf
    $pdf->tbOuputData();
    
    //draw the Table Border
    $pdf->tbDrawBorder(); 
  }

?>