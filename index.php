<?php
/**
 * The Unzipper extracts .zip or .rar archives and .gz files on webservers.
 * It's handy if you do not have shell access. E.g. if you want to upload a lot
 * of files (php framework or image collection) as an archive to save time.
 * As of version 0.1.0 it also supports creating archives.
 *
 * @author  Andreas Tasch, at[tec], attec.at; Stefan Boguth, Boguth.org
 * @license GNU GPL v3
 * @version 0.2.4
 */
define('VERSION', '0.2.4');
$GLOBALS['status'] = array();
include 'resources/classes/unzip.php';
$unzipper = new Unzipper;

if (isset($_POST['dounzip'])) {
  $postcont = "unzip";
}
if (isset($_POST['dozip'])) {
  $postcont = "zip";
}
if (isset($_POST['upload'])) {
  $postcont = "upload";
}
if (isset($_POST['explorer'])) {
  $postcont = "explorer";
} else {
  $setexp = false;
}

switch ($postcont) {
    case upload:
        include 'resources/classes/uploader.php';
        Uploader::doUpload();
        break;
    case explorer:
        include 'resources/classes/explorer.php';
        $exppath = !empty($_POST['exppath']) ? strip_tags($_POST['exppath']) : '.';
        $setexp = true;
        if (!empty($_POST['exppath'])) {
          $GLOBALS['status'] = array('success' => 'Verzeichnis erfolgreich geöffnet.');
        }
        else {
          $GLOBALS['status'] = array('error' => 'Kein Pfad angegeben!');
        }
        break;
    case zip:
        include 'resources/classes/zipper.php';
        $zippath = !empty($_POST['zippath']) ? strip_tags($_POST['zippath']) : '.';
        // Resulting zipfile e.g. zipper--2016-07-23--11-55.zip
        $zipfile = 'zipper-' . date("Y-m-d--H-i") . '.zip';
        Zipper::zipDir($zippath, $zipfile);
        break;
    case unzip:
        $archive = isset($_POST['zipfile']) ? strip_tags($_POST['zipfile']) : '';
        $destination = isset($_POST['extpath']) ? strip_tags($_POST['extpath']) : '';
        $unzipper->prepareExtraction($archive, $destination);
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Zippy</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
  <link rel="stylesheet" href="resources/animate.css">
  <link rel="stylesheet" href="resources/style.css">
  <script
			  src="https://code.jquery.com/jquery-3.1.1.min.js"
			  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
			  crossorigin="anonymous">
  </script>
  <script type="text/javascript" src="resources/app.js"></script>
  <link rel="shortcut icon" href="resources/favicon.ico" type="image/x-icon">
  <link rel="icon" href="resources/gfx/favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="animated zoomIn wrapper">
    <div class="innerwrapper">
      <p class="status status--<?php echo strtoupper(key($GLOBALS['status'])); ?>">
        <b>Status:</b> <?php echo reset($GLOBALS['status']); ?><br/>
      </p>
      <div id="logo">
        <img src="resources/gfx/logo.png" alt="Logo" />
      </div>
      <form action="" method="POST">
        <fieldset class="field">
          <h1>Archiv Unzipper</h1><div class="icon"></div>
            <div class="innercont animated fadeIn hide">
              <label for="zipfile">Wählen Sie ein .rar, .zip oder .gz-Archiv das sie entpacken wollen:</label>
              <select name="zipfile" size="1" class="select">
                <?php foreach ($unzipper->zipfiles as $zip) {
                  echo "<option>$zip</option>";
                }
                ?>
              </select>
                <?php if (count($unzipper->zipfiles) == 0) {
                        echo "<b>Keine Dateien zum entpacken vorhanden!</b>";
                      };
                ?>
              <p class="info">Die zu entpackenden Archive müssen sich in folgendem Pfad befinden: <?php echo __DIR__ ?></p>
              <label for="extpath">Pfad zum Entpacken (Optional):</label>
              <input type="text" name="extpath" class="form-field" />
              <p class="info">Den gewünschten Pfad ohne Slash am Anfang oder Ende eingeben (z.B. "meinPfad").<br> Wenn das Feld leergelassen wird dann wird das Archiv im selben Pfad entpackt.</p>
              <input type="submit" name="dounzip" class="submit" value="Entpacken"/>
            </div>
        </fieldset>

        <fieldset class="field">
          <h1>Archiv Zipper</h1><div class="icon"></div>
            <div class="innercont animated fadeIn hide">
              <label for="zippath">Pfad den Sie zippen wollen (Optional):</label>
              <input type="text" name="zippath" class="form-field" />
              <p class="info">Den gewünschten Pfad ohne Slash am Anfang oder Ende eingeben (z.B. "meinPfad").<br> Wenn das Feld leergelassen wird dann wird der aktuelle Pfad verwendet.</p>
              <input type="submit" name="dozip" class="submit" value="Packen"/>
            </div>
          </fieldset>
          </form>
          <form action ="" method="POST" enctype="multipart/form-data">
            <fieldset class="field">
              <h1>Archiv-Uploader</h1><div class="icon"></div>
              <div class="innercont animated fadeIn hide">
                <label for="uploader">Hochzuladende Datei:</label>
                <input type="file" name="uploaded" class="form-field" />
                <p class="info">Der Uploader benötigt Schreibrechte im Verzeichnis! Die Dateien werden in den Pfad Upload verschoben.<br> <b>Der Uploader akzeptiert nur .rar, .zip und .gz-Dateien mit maximal 10MB.</b></p>
                <input type="submit" name="upload" class="submit" value="Hochladen"/>
            </div>
          </fieldset>
        </form>
      <form action="" method="POST">
        <fieldset class="field">
          <h1>Datei-Explorer (beta)</h1><div class="<?php
          if($setexp == true) {
            echo ('icon icon-up');
          }
          else {
            echo ('icon');
          } ?>"></div>
            <div class="innercont<?php
            if($setexp == true) {
              echo ('');
            }
            else {
              echo (' hide');
            } ?> animated fadeIn">
              <label for="exppath">Anzuzeigender Pfad:</label>
              <input type="text" name="exppath" class="form-field" />
              <p class="info">Sie navigieren vom Verzeichnis des Scriptes aus, verwenden sie also z.B. ../ um eine Ebene höher zu springen.</p>
              <input type="submit" name="explorer" class="submit" value="Anzeigen"/>
              <?php
              if ($setexp == true && strlen($exppath) > 1) {
                echo ("<p>Geöffneter Pfad: " . $exppath . " </p>");
                Explorer::createExplorer($exppath);
              }
               ?>
            </div>
          </fieldset>
        </form>

      <p class="version">Unzipper Version: <?php echo VERSION; ?></p>
    </div>
  </div>
</body>
</html>
