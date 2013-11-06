$(document).ready(function() {
  $.ajaxSetup({ cache: false });
  $('#gest_conges_date_deb').datepicker();
  $("#gest_conges_date_fin").datepicker();
  
  $(".lien_modif").click(
    function()
    {
      $("#post_message").remove();
      var id = getId(this);
      var zone_cachee = $("#infos_cachees_" + id);
      var nom = getContenu(zone_cachee, "nom");
      var prenom = getContenu(zone_cachee, "prenom");
      var sections = getContenu(zone_cachee, "sections");
      var section_principale = getContenu(zone_cachee, "section_principale");
      var groupes = getContenu(zone_cachee, "groupes");
      remplit_zone_modif(id, nom, prenom, sections, section_principale, groupes);
      $("#modif_perso").css("visibility", "visible");
      return false;
    }
  );
  
  $("#valid_modif").click(
    function()
    {
      // on doit récupérer les valeurs saisies dans le formulaire
      var id = $("#form_id").attr("value");
      var action = $("#form_action").attr("value");
      var nom = $("#form_nom").attr("value");
      var prenom = $("#form_prenom").attr("value");
      var section_principale = $("#form_section_principale").attr("value");
      var sections = "";
      $("input[class=chk_sect]").each(
        function()
        {
          if ($(this).attr("checked"))
          {
            var id_section = $(this).attr("value");
            sections = sections + id_section + ",";
          }
        }
      );
      
      var groupes = "";
      $("input[class=chk_gpe]").each(
        function()
        {
          if ($(this).attr("checked"))
          {
            var id_groupe = $(this).attr("value");
            groupes = groupes + id_groupe + ",";
          }
        }
      );
      // On va mettre à jour les éléments de la zone cachée
      $("#infos_cachees_" + id).find(".nom").html(nom);
      $("#infos_cachees_" + id).find(".prenom").html(prenom);
      $("#infos_cachees_" + id).find(".sections").html(sections);
      $("#infos_cachees_" + id).find(".section_principale").html(section_principale);
      $.ajax({
        url: "pe_maj.php",
        data:"action="+action+"&id="+id+"&nom="+nom+"&prenom="+prenom+"&sections="+sections+"&section_principale="+section_principale+"&groupes="+groupes,
        success: function(resultat)
        {
          if (resultat == "1")
          {
            if (action == "creation")
            {
              window.location.href = "perso.php?post_message=creation";
            }
            else
            {
              window.location.href = "perso.php?post_message=modification&id=" + id;
            }
          }
          else
          {
            $("#msg_modif").css("color", "red");
            $("#msg_modif").css("font-weight", "bold");
            $("#msg_modif").show();
            $("#msg_modif").html("Erreur lors de la sauvegarde");
          }
        },
        error: function()
        {
          $("#msg_modif").css("color", "red");
          $("#msg_modif").css("font-weight", "bold");
          $("#msg_modif").show();
          $("#msg_modif").html("Erreur lors de la sauvegarde");
        }
      });
      return false;
    }
  )
  
  $(".lien_suppr").click(
    function()
    {
      $("#post_message").remove();
      var id = getId(this);
      var msg = "Êtes-vous sûr de vouloir supprimer\n";
      var zone_cachee = $("#infos_cachees_" + id);
      msg = msg + getContenu(zone_cachee, "nom") + " " + getContenu(zone_cachee, "prenom");
      if (confirm(msg))
      {
        $.ajax({
          url: "pe_maj.php?action=suppression&id=" + id,
          success: function(resultat)
          {
            if (resultat == "1")
            {
              window.location.href = "perso.php?post_message=suppression";
            }
          }
        });
      }
      return false;
    }
  );
  
  $(".confirm_necessaire").click(
    function()
    {
      var msg = "Êtes-vous sûr de vouloir effectuer cette opération ?\n";
      if (confirm(msg))
      {
        
      }
      else
      {
        // On annule la suppression
        return false;
      }
    }
  );
  
  
  $(".lien_conges").click(
    function()
    {
      $("#post_message").remove();
      alert("CONGES");
      return false;
    }
  );
  
  $("#lien_creation").click(
    function()
    {
      remplit_zone_creation();
      $("#modif_perso").css("visibility", "visible");
      $("#form_nom").focus();
      return false;
    }
  );
  
  /*
    Gestion des congés permanents
 */
  $("#a_add_conges_perm").click(
    function()
    {
      $("#gest_conges_perm").css("visibility", "visible");
      $("#gest_conges_perm_titre_form").html("Créer un congés");
      $("#gest_conges_perm_action").attr("val", "creation");
      $("#gest_conges_perm_horaire").attr("value", "");
      $("#gest_conges_perm_jour").attr("value", "");
      return false;
    }
  );

  $(".a_modif_conges_perm").click(
    function()
    {
      var id_conges = $(this).parent().parent().attr("id");
      id_conges = id_conges.replace("conges_perm_", "");
      
      var liste_td = $(this).parent().parent().find("td");
      var jour      = liste_td[0].innerHTML;
      var tranche = $(liste_td[1]).attr("class").replace("tranche_", "");
      $("#gest_conges_perm_id_conges").attr("value", id_conges);
      $("#gest_conges_perm").css("visibility", "visible");
      $("#gest_conges_perm_titre_form").html("Modifier un congés");
      $("#gest_conges_perm_action").attr("val", "modification");
      $("#gest_conges_perm_horaire").attr("value", tranche);
      $("#gest_conges_perm_jour").attr("value", jour);
      return false;
    }
  );

  $("#gest_conges_perm_valid_modif").click(
    function()
    {
      var action    = $("#gest_conges_perm_action").attr("val");
      var id_perso  = $("#gest_conges_perm_id_perso").attr("value");
      var id_conges  = $("#gest_conges_perm_id_conges").attr("value");
      var jour      = $("#gest_conges_perm_jour").attr("value");
      var horaire   = $("#gest_conges_perm_horaire").attr("value");
      
      $.ajax({
        url: "pe_conges_perm.php?action=" + action + "&id_perso=" + id_perso + "&jour=" + jour + "&tranche=" + horaire + "&id_conges=" + id_conges,
        success: function(resultat)
        {
          if (resultat == "1")
          {
            
            window.location.href = "perso.php?action=liste_conges&post_message=" + action + "&id=" + id_perso;
          }
          else
          {
            alert("Erreur dans la modification : " + resultat + "]");
          }
        }
      });
      return false;
    }
  );
  
  /*
    Gestion des congés ponctuels
  */
  $("#a_add_conges").click(
    function()
    {
      $("#gest_conges").css("visibility", "visible");
      $("#gest_conges_action").val("creation");
      $("#gest_conges_date_deb").val("");
      $("#gest_conges_date_fin").val("");
      $("#gest_conges_periode_deb").val("08:00");
      $("#gest_conges_periode_fin").val("20:00");
      return false;
    }
  );

  $(".a_modif_conges").click(
    function()
    {
      var id_conges = $(this).parent().parent().attr("id");
      id_conges = id_conges.replace("conges_", "");
      var td_deb = $(this).parent().parent().find("td")[0];
      var periode_deb = $(td_deb).find(".periode")[0].innerHTML;
      var date_deb = $(td_deb).find(".date")[0].innerHTML;
      
      var td_fin = $(this).parent().parent().find("td")[1];
      var periode_fin = $(td_fin).find(".periode")[0].innerHTML;
      var date_fin = $(td_fin).find(".date")[0].innerHTML;
      
      $("#gest_conges").css("visibility", "visible");
      $("#gest_conges_date_deb").val(date_deb);
      $("#gest_conges_date_fin").val(date_fin);
      $("#gest_conges_periode_deb").val(periode_deb);
      $("#gest_conges_periode_fin").val(periode_fin);
      $("#gest_conges_action").val("modification");
      $("#gest_conges_id_conges").val(id_conges);
      return false;
    }
  );
  
  $("#gest_conges_valid").click(
    function()
    {
      var action = $("#gest_conges_action").val();

      var date_deb    = $("#gest_conges_date_deb").val();
      var periode_deb = $("#gest_conges_periode_deb").val();
      var date_fin    = $("#gest_conges_date_fin").val();
      var periode_fin = $("#gest_conges_periode_fin").val();
      var id_conges   = $("#gest_conges_id_conges").val();

      if ( (date_fin == "") || (date_deb == "") )
      {
        alert("Merci de saisir la date de début ET la date de fin");
        return false;
      }

      var id_perso    = $("#gest_conges_id_perso").val();
      $.ajax({
        url: "pe_conges.php?action=" + action + "&id_perso=" + id_perso + "&date_deb=" + date_deb + "&periode_deb=" + periode_deb + "&date_fin=" + date_fin + "&periode_fin=" + periode_fin + "&id_conges=" + id_conges,
        success: function(resultat)
        {
          if (resultat == "1")
          {
            
            window.location.href = "perso.php?action=liste_conges&post_message=" + action + "&id=" + id_perso;
          }
          else
          {
            alert("Erreur dans la modification : " + resultat + "]");
          }
        }
      });


      return false;
    }
  );
  
  function getId(a)
  {
    return $(a).parent().parent().attr("id").substring(6);
  }
  
  function getContenu(zone, code)
  {
    if ( $(zone).find("." + code) )
    {
      return $(zone).find("." + code).html();
    }
  }
  
  function remplit_zone_creation()
  {
    $("#form_action").attr("value", "creation");
    $("#legend_form").html("Création");
    $("#form_id").attr("value", "");
    $("#form_nom").attr("value", "");
    $("#form_prenom").attr("value", "");
    $("input[type=checkbox]").attr("checked", false);
    $("input[type=submit]").attr("value", "Ajouter");
  }
  
  function remplit_zone_modif(id, nom, prenom, sections, section_principale, groupes)
  {
    $("#form_action").attr("value", "modification");
    $("#legend_form").html("Modification");
    $("#form_id").attr("value", id);
    $("#form_nom").attr("value", nom);
    $("#form_prenom").attr("value", prenom);
    // Gestion de la section principale
    $('#section_principale option').attr("selected", "");
    $('#section_principale option[value=""]').attr("selected", "selected");
    $('#section_principale option[value='+section_principale+']').attr("selected", "selected");
    // Gestion des sections attribuées
    $("input[type=checkbox]").attr("checked", false);
    var tab_sections = sections.split(",");
    for (s in tab_sections)
    {
      $("#sect_chk_" + tab_sections[s]).attr("checked", true);
    }
    
    var tab_groupes = groupes.split(",");
    for (g in tab_groupes)
    {
      $("#gpe_chk_" + tab_groupes[g]).attr("checked", true);
    }
    
    $("input[type=submit]").attr("value", "Modifier");
    $("#form_nom").focus();
  }
})