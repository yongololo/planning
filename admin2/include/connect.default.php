<?php
  // Cette page gère la connexion à la base de données (et c'est tout !)
  $host = '';
  $user = '';
  $passe = '';
  $base = '';
  mysql_connect($host,$user,$passe);
  mysql_select_db($base);
  mysql_query("SET NAMES utf8");
?>
