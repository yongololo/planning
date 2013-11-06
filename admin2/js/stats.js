$(document).ready(function() {
  $.ajaxSetup({ cache: false });
  $("#intervalle_deb").datepicker();
  $("#intervalle_fin").datepicker();
  
  $(".lien_cache").click(
    function()
    {
      $(this).parent().next().toggle();
      if ($(this).html() == "(masquer)")
      {
        $(this).html("(afficher)");
      }
      else
      {
        $(this).html("(masquer)");
      }
      return false;
    }
  );
  
  $( "#tabs_stats" ).tabs();

});