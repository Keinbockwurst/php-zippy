$( document ).ready(function() {
  $('.file:even').addClass('even');
  $( ".field h1, .icon" ).click(function() {
    $(this).parent().find(".innercont").toggleClass("hide");
    $(this).parent().find(".icon").toggleClass("icon-up");
  });
  $( "#uploadclick" ).click(function(e) {
    var input, file;

      if (!window.FileReader) {
          alert("The file API isn't supported on this browser yet.");
          return;
      }

      input = document.getElementById('fileinput');
      file = input.files[0];
      if (file.size > 1024 * 1024 * 10) {
          alert("Die Datei ist größer als 10MB!");
          e.preventDefault();
      }
      else {
        //
      }
    });
});
