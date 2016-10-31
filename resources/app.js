$( document ).ready(function() {
  $(function() {
    $(".innercont").addClass("hider");
  });
  $( ".field" ).click(function() {
    $(this).parent().find(".innercont").toggleClass("hider");
  });
});
