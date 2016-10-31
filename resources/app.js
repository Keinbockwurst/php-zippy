$( document ).ready(function() {
  $(function() {
    $(".innercont").addClass("hider");
  });
  $( ".field h1, .icon" ).click(function() {
    $(this).parent().find(".innercont").toggleClass("hider");
    $(this).parent().find(".icon").toggleClass("icon-up");
  });
});
