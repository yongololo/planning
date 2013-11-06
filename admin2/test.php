<?php
function get_lundi_dimanche_from_week($week,$year)
{
if(strftime("%W",mktime(0,0,0,01,01,$year))==1)
$mon_mktime = mktime(0,0,0,01,(01+(($week-1)*7)),$year);
else
$mon_mktime = mktime(0,0,0,01,(01+(($week)*7)),$year);

if(date("w",$mon_mktime)>1)
$decalage = ((date("w",$mon_mktime)-1)*60*60*24);

$lundi = $mon_mktime - $decalage;
$dimanche = $lundi + (6*60*60*24);

return array(date("D - d/m/Y",$lundi),date("D - d/m/Y",$dimanche));
}

$tmp = get_lundi_dimanche_from_week("50","2009");
print $tmp[0]."<BR>"; // date du lundi
print $tmp[1]."<BR>"; // date du mardi


// function calculSemaine($leNumSemaineSaisi, $lAnneeSaisie)
function calculSemaine($semaine, $annee)
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

$t = calculSemaine("07", "2010");
foreach ($t as $d)
{
  print date("Y-m-d", $d)."<BR/>";;
}
?>