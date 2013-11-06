<?php
  session_start();
  $erreur = "";
  $redir = "";
  if (isset($_GET["redir"]))
  {
    $redir = $_GET["redir"];
  }
  elseif (isset ($_POST["redir"]))
  {
    $redir = $_POST["redir"];
  }
  else
  {
    $redir = "index.php";
  }
  
  
  if (isset($_POST["login"]))
  {
    // On va vérifier que l'utilisateur est bon
    include("include/connect.php");
    include("include/utils.php");

    $req = "select * from utilisateurs where login='".$_POST["login"]."' and pass='".md5($_POST['mdp'])."';";
    $res = SQL($req);
    if (mysql_numrows($res) > 0)
    {
      $row = mysql_fetch_assoc($res);
      
      $_SESSION["login"]      = $_POST["login"];
      $_SESSION["id"]         = $row["id_user"];
      $_SESSION["privilege"]  = $row["privilege"];
      
      if ($redir != "")
      {
        header("Location: ".urldecode($redir));
        return;
      }
      else
      {
        header("Location: index.php");
        return;
      }
    }
    else
    {
      // On va regarder si ce n'est pas un utilisateur de type A/B
      $req = "select * from personnel where UPPER(nom)='".strtoupper($_POST["login"])."' and pass='".md5($_POST['mdp'])."' and autogestion='1';";
      $res = SQL($req);
      if (mysql_numrows($res) > 0)
      {
        $row = mysql_fetch_assoc($res);
        $_SESSION["login"]      = $row["nom"];
        $_SESSION["privilege"]  = "autogestion";
        $_SESSION["id"]         = $row["id"];
        if ($redir != "")
        {
          header("Location: ".urldecode($redir));
          return;
        }
        else
        {
          header("Location: index.php");
          return;
        }

      }
      else
      {
        $erreur = 1;  
      }
    }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Authentification nécessaire</title>
  <style type='text/css'>
    form
    {
      width:250px;
      margin:auto;
      border:2px solid #CCC;
      padding:10px;
    }
    
    label
    {
      display: block;
      width: 100px;
      float: left;
      padding-right: 1%;
      text-align: right;
      letter-spacing: 1px;
      color:#333;
    }
    
    h1
    {
      font-size:16px;
      text-align:center;
      padding-bottom:10px;
      border-bottom:2px solid #CCC;
    }
    
    center
    {
      border-top:2px solid #CCC;
      padding-top:10px;
    }
    
    input[type='text']
    {
      text-align:center;
    }
    
    input[type='submit']
    {
      color:#333;
    }
    
    #erreur
    {
      color:red;
    }
  </style>
</head>
<body>
  <form method='post' action='auth.php' name='myform'>
    <input type='hidden' name='redir' value='<?php echo $redir; ?>'/>
    <h1><img src='../img/icon_key.gif'/>&nbsp;&nbsp;Gestion de plannings - Connexion</h1>
    <label>Login : </label><input type='text' name='login' value=''/><br/>
    <label>Mot de passe: </label><input type='text' name='mdp' value=''/><br/><br/>
    <?php
      if ($erreur)
      {
        print "<span id='erreur'>Login ou mot de passe incorrect</span>";
      }
    ?>
    <center><input type='submit' value='Valider'/></center>
  </form>
  <center style='border:0px'><a href='../'>Revenir à la consultation</a></center>
  <script type='text/javascript'>
    document.myform.login.focus();
  </script>
</body>
</html>