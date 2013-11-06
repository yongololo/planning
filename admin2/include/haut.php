<?php
  // On va gérer ici les sessions !
  session_start();
  if ((!isset($_SESSION['login'])) || (empty($_SESSION['login'])))
	{
		// la variable 'login' de session est non déclaré ou vide
		header("Location: auth.php");
	}
?>

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
  <script type='text/javascript' src='js/jquery-1.4.4.min.js'></script>
	<script type='text/javascript' src='js/jquery-ui-1.8.7.custom.min.js'></script>
  <!-- <script type='text/javascript' src="js/jquery-ui-1.8.2.custom.min.js"></script> -->
  <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js" type="text/javascript"></script> -->
  <script type='text/javascript' src="js/jquery.ui.datepicker-fr.js"></script>
  <?php
    // On gérer les inclusions spécifiques à la page
    if ( isset($code_page) )
    {
      print "<link rel='stylesheet' type='text/css' href='css/".$code_page.".css' media='all' />\n";
      print "<script type='text/javascript' src='js/".$code_page.".js'></script>\n";
      
      if ($code_page == "planning")
      {
        print "<link rel='stylesheet' type='text/css' href='../css/sections.css' media='all' />\n";  
      }
    }
  ?>
  <link href="css/ui-lightness/jquery-ui-1.8.7.custom.css" rel="stylesheet" type="text/css"/>
</head>
<body>
  <?php
    include("menu.php");
  ?>