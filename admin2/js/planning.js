$(document).ready(function() {
  $.ajaxSetup({ cache: false });
  $(".modif_case").live("click", 
    function()
    {
      var code_case = $(this).parent().attr("id");
      var codes = code_case.match("(.*)_(.*)");
      
      var contenu_case_orig =  $("#" + code_case).html();

      if (codes)
      {
        var section = codes[1];
        var horaire = codes[2];
        // On connait la section et l'horaire, on va interroger une
        // page pour connaitre les personnels potentiellement disponibles pour cette zone.
        $.getJSON("pl_persos_dispos.php?id_s=" + section + "&id_h=" + horaire + "&stamp=" + $("#stamp_modif").attr("title"),
          function(data)
          {
            // On récupère pour la case donnée une liste des personnels susceptibles de correspondre
            var liste = "";
            for (var i in data)
            {
              var poste = data[i];
              if (liste != "")
              {
                liste = liste + "<br/>";
              }
              liste = liste + "<select id='select'>\n";
              liste = liste + "<option value=''></option>";
              for (var j in poste)
              {
                var personne = poste[j];
                liste = liste + "<option value='" + personne["id"] + "'";
                if (personne["select"])
                {
                  liste = liste + " selected";
                }
                liste = liste + ">" + personne["nom"] + "</option>\n";
              }
              liste += "</select>";
            }
            // On ajouter l'image pour valider la modification faite
            liste += "<br/><a href='#' class='annule_case'><img src='img/cancel2.png' title='valider' id='annule_case_" + code_case + "'/></a>";
            liste += "<a href='#' class='valid_case'><img src='img/valid.png' title='valider' id='valid_case_" + code_case + "'/></a>";
            
            // On va aussi stocker la valeur initiale pour la remettre si jamais on annule
            liste += "<input class='val_orig' type='hidden' value='TOTO'/>";
            
            $("#" + code_case).html(liste);
            
            $("#valid_case_" + code_case).click(
              function()
              {
                valid_maj_case(code_case, section, horaire);
                // $("#debug_zone").append("<br/>MAJ OK");
                return false;
              }
            );
            
            $("#annule_case_" + code_case).click(
              function()
              {
                $("#" + code_case).html(contenu_case_orig);
              }
            );
          }
        );
      }
      return false;
    }
  );
  
  $(".case_obs").click(
    function()
    {
//      var contenu_orig = $(this).find(".obs")[0].html();
      var contenu_orig = $(this).parent().find(".obs").html();
      contenu_orig = contenu_orig.replace("&nbsp;", "");
      var id_lien = $(this).attr('id');
      if (!contenu_orig.match("<input"))
      {
        var contenu = "<input type='text' id='value_obs_" + id_lien + "' value='" + contenu_orig + "'>";
        contenu += "&nbsp;<a href='#' class='valid_case'><img src='img/valid.png' title='valider' id='valid_case_" + id_lien + "'/></a>";
        contenu += "<br/>Car. restants : <span id='nb_car_value_obs_" + id_lien + "'>" + (50 - contenu_orig.length) + "</span>\n";
        $(this).parent().find(".obs").html(contenu);
        
        $("#valid_case_" + id_lien ).click(
          function()
          {
            valid_maj_case_obs(id_lien, "value_obs_" + id_lien);
            return false;
          }
        );
        
        $("#value_obs_" + id_lien).keypress(
          function(e)
          {
            var taille = $("#value_obs_" + id_lien).val().length;

            if (e.which == 8)
            {
              // Touche backspace
              $("#nb_car_value_obs_" + id_lien).html(49 - taille);
              $("#nb_car_value_obs_" + id_lien).css("color", "");
            }
            else if (e.which == 13)
            {
              valid_maj_case_obs(id_lien, "value_obs_" + id_lien);
            }
            else if (taille >= 51)
            {
              $("#nb_car_value_obs_" + id_lien).html(49 - taille);
              $("#nb_car_value_obs_" + id_lien).css("color", "red");
              return;
            }
            else
            {
              $("#nb_car_value_obs_" + id_lien).html(49 - taille);              
            }
          }
        );
      }
      return false;
    }
  );
  
  // Au chargement de la page, on va vérifier toutes les erreurs
  // de doublons
  verifPlanning();
});

function valid_maj_case_obs(id_case, obs)
{
  var codes = id_case.match(".*_(.*)");
  var horaire = codes[1];
  var params = "id_s=OBS&id_h=" + horaire + "&stamp=" + $("#stamp_modif").attr("title") + "&contenu=" + $("#value_obs_" + id_case).val();

  $.ajax({
    type: "GET",
    url: "pl_maj_creneau.php",
    data: params,
    success: function(msg)
    {
      // La mise à jour s'est déroulée avec succès. On va transformer le contenu de la case
      // qui est une liste de select en affichage
      $("#" + id_case).parent().find(".obs").html($("#value_obs_" + id_case).val());
    },
    error: function(msg)
    {
      alert("Erreur de mise à jour");
    }
  });
}

function valid_maj_case(id, section, horaire)
{
  var params = "id_s=" + section + "&id_h=" + horaire + "&stamp=" + $("#stamp_modif").attr("title");
  var liste_fixe = "<ul class='ul_h_" + horaire + " ul_s_" + section + "'>";

  $("#" + id).find("select").each(
    function(i)
    {
      params = params + "&persos[" + i + "]=" + $(this).attr("value");
      liste_fixe = liste_fixe + "<li class='perso_" + $(this).attr("value") + "'>" + $(this).find(":selected").html() + "</li>";
    }
  );
  liste_fixe += "</ul>";

  $.ajax({
    type: "GET",
    url: "pl_maj_creneau.php",
    data: params,
    success: function(msg)
    {
      if (msg != "")
      {
        alert(msg);
      }

      // La mise à jour s'est déroulée avec succès. On va transformer le contenu de la case
      // qui est une liste de select en affichage
      $("#" + id).html("<a href='#' class='modif_case'><img src='img/modify.png' alt='modifier' title='modifier' style='float:right'/></a>\n" + liste_fixe);
      detecteCollisions(horaire);
    },
    error: function(msg)
    {
      alert("Erreur de mise à jour");
    }
  });
}

function verifPlanning()
{
  $(".entete_planning").each(
    function()
    {
      var id_T = $(this).attr("id").substring(4);
      detecteCollisions(id_T);
    }
  )
}

function detecteCollisions(horaire)
{
  var collision = Array();
  var presents  = Array();
  $(".ul_h_" + horaire).each(
    function(i)
    {
      $(this).find("li").each(
        function(j)
        {
          var ma_classe = $(this).attr("class");
          if (presents[ma_classe])
          {
            collision[ma_classe] = 1;
          }
          presents[$(this).attr("class")] = 1;
        }
      );
    }
  );

  // On a un tableau collision qui contient tous
  // les personnages qui posent problème
  $(".ul_h_" + horaire).find("li").css("color", "black");
  $(".ul_h_" + horaire).find("li").css("font-weight", "normal");
  
  for (var ma_classe in collision)
  {
    $(".ul_h_" + horaire).find("." + ma_classe).css("color", "red");
    $(".ul_h_" + horaire).find("." + ma_classe).css("font-weight", "bold");
  } 
}