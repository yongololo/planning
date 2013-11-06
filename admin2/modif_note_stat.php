<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>
<body style='width:700px; margin:auto; margin-top:50px; text-align:center'>
  
<?php
  if ( isset($_POST["action"]) )
  {
    $action = $_POST["action"];
  }
  else
  {
    $action = "";
  }

  if ($action == "enreg")
  {
    $handle = fopen("info_stat.txt", "w");
    $contenu = $_POST["contenu"];
    $contenu = stripslashes($contenu);
    fwrite($handle, $contenu);
    print "<div style='font-weight:bold; color:red;'>Modification effectuée</div><br/><br/><br/>";
  }

  # On doit afficher le contenu simplement.
  print "<form method='post'>\n";
  print "<input type='hidden' name='action' value='enreg'/>";
  print "<textarea rows='10' cols='70' name='contenu'>";
  
  # On va voir si le fichier contient quelque chose
  $contenu = "";
  if (file_exists("info_stat.txt"))
  {
    $handle = fopen("info_stat.txt", "r");
    $taille = filesize("info_stat.txt");
    $taille++;
    $contenu = fread($handle, $taille);
  }
  
  print $contenu;
  
  print "</textarea><br/><br/>";
  print "<input type='submit' value='Valider'/>";
  print "</form>\n";

?>
<a href='stats.php'>Revenir à la page des stats</a>
</body>
</html>