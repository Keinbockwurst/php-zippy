<?php
/**
 * The Unzipper extracts .zip or .rar archives and .gz files on webservers.
 * It's handy if you do not have shell access. E.g. if you want to upload a lot
 * of files (php framework or image collection) as an archive to save time.
 *
 * @author  Andreas Tasch, at[tec], attec.at; Stefan Boguth, Boguth.org
 * @license GNU GPL v3
 * @version 0.2.9
 */
define('VERSION', '0.2.9');
if (!file_exists('files/')) {
    mkdir('files/', 0755);
}


include 'resources/classes/unzip.php';
$unzipper = new Unzipper();
session_start();
$_SESSION['status'] = array();
$_SESSION['status'] = array('info' => 'Zippy initialized. Waiting for further instructions.');
$postcont = null;

if (isset($_POST['dounzip'])) {
    $postcont = 'unzip';
}
if (isset($_POST['dozip'])) {
    $postcont = 'zip';
}
if (isset($_POST['upload'])) {
    $postcont = 'upload';
}
if (isset($_POST['explorer'])) {
    $postcont = 'explorer';
} else {
    $setexp = false;
}


switch ($postcont) {
    case 'upload':
        include 'resources/classes/uploader.php';
        Uploader::doUpload();
        break;
    case 'explorer':
        $exppath = !empty($_POST['exppath']) ? strip_tags($_POST['exppath']) : '.';
        if (is_dir($exppath) == false) {
            $_SESSION['status'] = array('error' => 'Fehler: Directory not found or not writeable!');
            $setexp = false;
            $docreate = false;
        } else {
          include 'resources/classes/explorer.php';
          $setexp = true;
          $docreate = true;
          if (empty($_POST['exppath'])) {
            $_SESSION['status'] = array('error' => 'Error: No path entered!');
          } else {
            $_SESSION['status'] = array('success' => 'Directory opened successfully!');
          }
        }
        break;
    case 'zip':
        include 'resources/classes/zipper.php';
        $zippath = !empty($_POST['zippath']) ? strip_tags($_POST['zippath']) : '.';
        // Resulting zipfile e.g. zipper--2016-07-23--11-55.zip
        $zipfile = 'files/zipper-'.date('Y-m-d--H-i').'.zip';
        Zipper::zipDir($zippath, $zipfile);
        break;
    case 'unzip':
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
  <style>
    body {
      font-family: 'Ubuntu', sans-serif;
      line-height: 150%;
      background-color: #cccccc;
    }
  </style>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="gray" />
  <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
  <link rel="stylesheet" href="resources/animate.css">
  <link rel="stylesheet" href="resources/style.css">
  <script
			  src="https://code.jquery.com/jquery-3.1.1.min.js"
			  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
			  crossorigin="anonymous">
  </script>
  <script type="text/javascript" src="resources/app.js"></script>
  <link rel="icon" href="resources/gfx/favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="animated fadeIn wrapper">
    <div class="innerwrapper">
      <p class="status status--<?php echo strtoupper(key($_SESSION['status'])); ?>">
        <b>Status:</b> <?php echo reset($_SESSION['status']); ?><br/>
      </p>
      <div id="logo" class="animated bounceIn">
        <img src="resources/gfx/logo.png" alt="Logo" />
      </div>
      <form action="" method="POST">
        <fieldset class="field">
          <h1>Archive Unzipper</h1><div class="icon"></div>
            <div class="innercont animated fadeIn hide">
              <label for="zipfile">Choose the archive you want to decompress:</label>
              <select name="zipfile" size="1" class="select">
                <?php foreach ($unzipper->zipfiles as $zip) {
                        echo "<option>$zip</option>";
                      }
                ?>
              </select>
                <?php if (count($unzipper->zipfiles) == 0) {
                        echo '<b>No files found!</b>';
                      }
                ?>
              <p class="info">The archives you want to decompress need to be placed in the path: <?php echo __DIR__ ?>/files/</p>
              <label for="extpath">Path to decompress to (Optional):</label>
              <input type="text" name="extpath" class="form-field" placeholder="<?php echo __DIR__ ?>" />
              <p class="info">Enter the path without slashes (e.g. "myPath").<br> When you leave this field empty the root-directory of Zippy is used.</p>
              <input type="submit" name="dounzip" class="submit" value="Decompress"/>
            </div>
        </fieldset>

        <fieldset class="field">
          <h1>Archive Zipper</h1><div class="icon"></div>
            <div class="innercont animated fadeIn hide">
              <label for="zippath">Path to be compressed (Optional):</label>
              <input type="text" name="zippath" class="form-field" placeholder="z.B. files"/>
              <p class="info">Enter the path without slashes (e.g. "myPath").<br> When you leave this field empty the root-directory of Zippy is used. <br>You'll find the file in <?php echo __DIR__ ?>/files/ after compression is done.</p>
              <input type="submit" name="dozip" class="submit" value="Compress"/>
            </div>
          </fieldset>
          </form>
          <form action ="" method="POST" enctype="multipart/form-data">
            <fieldset class="field">
              <h1>Archive-Uploader</h1><div class="icon"></div>
              <div class="innercont animated fadeIn hide">
                <label for="uploader">File for Upload:</label>
                <input type="file" name="uploaded" class="form-field" id="fileinput" />
                <output id="list"></output>
                <p class="info">The Uploader needs to be able to write inside ../files.<br> <b>Only .rar, .gz and .zip files with a maximum file size of <?php echo ("<span id='maxsize' value='" . ini_get('upload_max_filesize') . "'</span>" . ini_get('upload_max_filesize') . "B.") ?></b></p>
                <input type="submit" name="upload" class="submit" value="Upload" id="uploadclick"/>
            </div>
          </fieldset>
        </form>
      <form action="" method="POST">
        <fieldset class="field">
          <h1>File-Explorer (alpha)</h1><div class="<?php
          if ($setexp == true) {
              echo 'icon icon-up';
          } else {
              echo 'icon';
          } ?>"></div>
            <div class="innercont<?php
            if ($setexp == true) {
                echo '';
            } else {
                echo ' hide';
            } ?> animated fadeIn">
              <label for="exppath">Path:</label>
              <input type="text" name="exppath" class="form-field" placeholder="z.B. files" />
              <p class="info">You are navigating from the root of Zippy!</p>
              <input type="submit" name="explorer" class="submit" value="Anzeigen"/>
              <?php
              if ($setexp == true && strlen($exppath) > 1 && $docreate = true) {
                  echo '<p>Geöffneter Pfad: '.$exppath.' </p>';
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
