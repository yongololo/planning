<?php
  if (isset($_GET["action"]))
  {
    $action=$_GET["action"];
  }
  else
  {
    $action = "";
  }
  
  if ($action == "logout")
  {
    session_start();
    session_destroy();
    header("Location: ../index.php");
    exit;
  }

  include("include/connect.php");
  include("include/utils.php");
  $code_page = "planning";
  include("include/haut.php");
  print "<h1>Application de gestion des plannings</h1>\n";
  print "<br style='clear:both'/>";
?>
<br/><br/>
<center><img src='img/planning.jpg'/></center>
<?php
  include("include/bas.php");
?>