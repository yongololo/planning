<?php
  // Cette page g�re la connexion � la base de donn�es (et c'est tout !)
  $host = '';
  $user = '';
  $passe = '';
  $base = '';
  mysql_connect($host,$user,$passe);
  mysql_select_db($base);
  mysql_query("SET NAMES utf8");
?>
