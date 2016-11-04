<?php
class Uploader {

  public function doUpload() {
      $directory = "upload/";
      if (strlen(pathinfo($_FILES['uploaded']['name'], PATHINFO_FILENAME)) == 0) {
        $GLOBALS['status'] = array('error' => 'Fehler: Sie müssen eine Datei zum hochladen auswählen!');
      }
      else {
        //checking if dir upload exists, if not create it.
        if (!file_exists($directory)) {
            mkdir($directory, 0755);
        }



        //moving the uploaded files to the upload-dir
        $upload_folder = 'upload/'; //Das Upload-Verzeichnis
        $filename = pathinfo($_FILES['uploaded']['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($_FILES['uploaded']['name'], PATHINFO_EXTENSION));

        //Überprüfung der Dateiendung
        function checkExt($extension) {
          $allowed_extensions = array('rar', 'zip', 'gz');
          if(!in_array($extension, $allowed_extensions)) {
            die("<p><b><font color='red' face='arial'>Fehler: </font><font face='arial'>Ungueltige Dateiendung. Nur .rar, .zip und .gz sind erlaubt.</font></b></p><input type='button' value='Verstanden' onClick='history.go(-1)'>");
          }
        }

        //Überprüfung des MIME-TYPE der hochgeladenen Datei
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['uploaded']['tmp_name']);
        finfo_close($finfo);

        if (strpos($mime, 'zip') !== false or strpos($mime, 'rar') !== false or strpos($mime, 'gzip' !== false)) {
          checkExt($extension);
        }
        else {
          die("<p><b><font color='red' face='arial'>Fehler: </font><font face='arial'>Ungueltige Datei! Nur .rar, .zip und .gz sind erlaubt.</font></b></p><input type='button' value='Verstanden' onClick='history.go(-1)'>");
        }

        //Überprüfung der Dateigröße
        $max_size = 10000*1024; //10 MB
        if($_FILES['uploaded']['size'] > $max_size) {
          die("<p><b><font color='red' face='arial'>Fehler: </font> <font face='arial'>Bitte keine Dateien größer 10 MB hochladen.</font></b></p><input type='button' value='Verstanden' onClick='history.go(-1)'>");
        }

        //Pfad zum Upload
        $new_path = $upload_folder.$filename.'.'.$extension;

        //Neuer Dateiname falls die Datei bereits existiert
        if(file_exists($new_path)) { //Falls Datei existiert, hänge eine Zahl an den Dateinamen
          $id = 1;
          do {
            $new_path = $upload_folder.$filename.'_'.$id.'.'.$extension;
            $id++;
          } while(file_exists($new_path));
        }
        $GLOBALS['status'] = array('success' => 'Datei erfolgreich hochgeladen.');
      }
    }
}
 ?>
