$( document ).ready(function() {
  $('.file:even').addClass('even');
  $( ".field h1, .icon" ).click(function() {
    $(this).parent().find(".innercont").toggleClass("hide");
    $(this).parent().find(".icon").toggleClass("icon-up");
  });
});
