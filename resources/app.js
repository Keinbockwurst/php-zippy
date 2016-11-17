$(document).ready(function() {
  $('.file:even').addClass('even');
  $(".field h1, .icon").click(function() {
    $(this).parent().find(".innercont").toggleClass("hide");
    $(this).parent().find(".icon").toggleClass("icon-up");
  });
  $("#uploadclick").click(function(e) {
    var input, file;

    if (!window.FileReader) {
      alert("The file API isn't supported on this browser yet.");
      return;
    }
     var filesize = parseInt($("#maxsize").text().slice(0,-3));
    input = document.getElementById('fileinput');
    file = input.files[0];
    if (file.size > 1024 * 1024 * $('#maxsize').text().slice(0,-3)) {
      alert("File is bigger than " + filesize + " MB!");
      e.preventDefault();
    } else {
      //nuttin
    }
  });
});
