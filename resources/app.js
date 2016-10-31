$( document ).ready(function() {
  $(function() {
    $(".innercont").addClass("hider");
  });
  $( ".field" ).click(function() {
    $(this).find(".innercont").toggleClass("hider");
  });
});
