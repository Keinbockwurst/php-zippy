<?php

class Explorer {
    public function createExplorer($exppath) {
      $ordner = $exppath;
      $alledateien = scandir($ordner);

      if (count($alledateien, COUNT_RECURSIVE) > 2) {
        echo "<form action ='' method='POST' enctype='multipart/form-data'>";
        foreach ($alledateien as $datei) {
            $dateiinfo = pathinfo($ordner.'/'.$datei);
            $size = ceil(filesize($ordner.'/'.$datei) / 1024);
            if ($datei != '.' && $datei != '..' && $datei != '_notes' && $bildinfo['basename'] != 'Thumbs.db') {

              //Buttons to add
              $delete = "<div class='deletebtn'><input type='checkbox' id='delete' class='deletefile' name='delete[]' value=".$dateiinfo['dirname'].'/'.$dateiinfo['basename']."><div class='deleteicon'></div></div>";
                  //Bildtypen sammeln
                  //$bildtypen = array('jpg', 'jpeg', 'gif', 'png');

                  //Dateien nach Typ prüfen, in dem Fall nach Endungen für Bilder filtern
                /*  if (in_array($dateiinfo['extension'], $bildtypen)) {
                      echo
                        "<div class='galerie'>
                          <a href='" .$dateiinfo['dirname'].'/'.$dateiinfo['basename']."'>
                          <img src='" .$dateiinfo['dirname'].'/'.$dateiinfo['basename']."' width='140' alt='Vorschau' /></a>
                          <span>" .$dateiinfo['filename']."(".$size." kb)<input type='checkbox' id='delete' class='deletefile' name='delete[]' value=".$dateiinfo['dirname'].'/'.$dateiinfo['basename']."></span>
                        </div>"
                      ;

                  // wenn keine Bildeindung dann normale Liste für Dateien ausgeben
                } hier normalerweise else */if (isset($datei)) {
                      echo "
                        <div class='file'>
                          <a href='" .$dateiinfo['dirname'].'/'.$dateiinfo['basename']."'>&raquo; ".$dateiinfo['filename']." </a> (".$dateiinfo['extension'].' | '.$size."kb)<div class='btn'>"/*add buttons in here*/.$delete."</div>
                        </div>";
                  }
              }
            }
            echo "<input type='submit' value='Gewählte Löschen' class='submit'>";
            echo "</form>";
      } else {
        echo "<p>-- Das Verzeichnis ist leer! --</p>";
      }
    }
}
