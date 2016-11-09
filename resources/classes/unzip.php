<?php

class Unzipper
{
    public $localdir = '.';
    public $zipfiles = array();
    public function __construct()
    {
        //read directory and pick .zip and .gz files
    if ($dh = opendir($this->localdir)) {
        while (($file = readdir($dh)) !== false) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip'
          || pathinfo($file, PATHINFO_EXTENSION) === 'gz'
          || pathinfo($file, PATHINFO_EXTENSION) === 'rar'
        ) {
                $this->zipfiles[] = $file;
            }
        }
        closedir($dh);
        /*
        if (!empty($this->zipfiles)) {
            $_SESSION['status'] = array('info' => '.zip, .gz oder .rar Dateien gefunden, bereit zum entpacken.');
        } else {
            $_SESSION['status'] = array('info' => 'Keine .zip oder .gz oder rar Dateien gefunden.');
        }*/
    }
    }
  /**
   * Prepare and check zipfile for extraction.
   *
   * @param $archive
   * @param $destination
   */
  public function prepareExtraction($archive, $destination)
  {
      // Determine paths.
    if (empty($destination)) {
        $extpath = $this->localdir;
    } else {
        $extpath = $this->localdir.'/'.$destination;
      // todo move this to extraction function
      if (!is_dir($extpath)) {
          mkdir($extpath);
      }
    }
    //allow only local existing archives to extract
    if (in_array($archive, $this->zipfiles)) {
        self::extract($archive, $extpath);
    }
  }
  /**
   * Checks file extension and calls suitable extractor functions.
   *
   * @param $archive
   * @param $destination
   */
  public static function extract($archive, $destination)
  {
      $ext = pathinfo($archive, PATHINFO_EXTENSION);
      switch ($ext) {
      case 'zip':
        self::extractZipArchive($archive, $destination);
        break;
      case 'gz':
        self::extractGzipFile($archive, $destination);
        break;
      case 'rar':
        self::extractRarArchive($archive, $destination);
        break;
    }
  }
  /**
   * Decompress/extract a zip archive using ZipArchive.
   *
   * @param $archive
   * @param $destination
   */
  public static function extractZipArchive($archive, $destination)
  {
      // Check if webserver supports unzipping.
    if (!class_exists('ZipArchive')) {
        $_SESSION['status'] = array('error' => 'Fehler: Ihre PHP-Version unterstützt keine unzip-Fähigkeiten.');

        return;
    }
      $zip = new ZipArchive();
    // Check if archive is readable.
    if ($zip->open($archive) === true) {
        // Check if destination is writable
      if (is_writeable($destination.'/')) {
          $zip->extractTo($destination);
          $zip->close();
          $_SESSION['status'] = array('success' => 'Dateien erfolgreich entpackt!');
      } else {
          $_SESSION['status'] = array('error' => 'Fehler: Verzeichnis nicht vom Webserver schreibbar.');
      }
    } else {
        $_SESSION['status'] = array('error' => 'Fehler: Archiv nicht lesbar!');
    }
  }
  /**
   * Decompress a .gz File.
   *
   * @param $archive
   * @param $destination
   */
  public static function extractGzipFile($archive, $destination)
  {
      // Check if zlib is enabled
    if (!function_exists('gzopen')) {
        $_SESSION['status'] = array('error' => 'Fehler: Your PHP has no zlib support enabled.');

        return;
    }
      $filename = pathinfo($archive, PATHINFO_FILENAME);
      $gzipped = gzopen($archive, 'rb');
      $file = fopen($filename, 'w');
      while ($string = gzread($gzipped, 4096)) {
          fwrite($file, $string, strlen($string));
      }
      gzclose($gzipped);
      fclose($file);
    // Check if file was extracted.
    if (file_exists($destination.'/'.$filename)) {
        $_SESSION['status'] = array('success' => 'Datei erfolgreich entpackt.');
    } else {
        $_SESSION['status'] = array('error' => 'Fehler beim entpacken der Datei.');
    }
  }
  /**
   * Decompress/extract a Rar archive using RarArchive.
   *
   * @param $archive
   * @param $destination
   */
  public static function extractRarArchive($archive, $destination)
  {
      // Check if webserver supports unzipping.
    if (!class_exists('RarArchive')) {
        $_SESSION['status'] = array('error' => 'Error: Your PHP version does not support .rar archive functionality. <a class="info" href="http://php.net/manual/en/rar.installation.php" target="_blank">How to install RarArchive</a>');

        return;
    }
    // Check if archive is readable.
    if ($rar = RarArchive::open($archive)) {
        // Check if destination is writable
      if (is_writeable($destination.'/')) {
          $entries = $rar->getEntries();
          foreach ($entries as $entry) {
              $entry->extract($destination);
          }
          $rar->close();
          $_SESSION['status'] = array('success' => 'Datei erfolreich entpackt.');
      } else {
          $_SESSION['status'] = array('error' => 'Fehler: Verzeichnis nicht vom Webserver schreibbar.');
      }
    } else {
        $_SESSION['status'] = array('error' => 'Error: Archiv nicht lesbar!');
    }
  }
}
?>
