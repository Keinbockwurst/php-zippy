<?php
/**
 * Class Zipper.
 *
 * Copied and slightly modified from http://at2.php.net/manual/en/class.ziparchive.php
 *
 * @author umbalaconmeogia
 */
class Zipper {
    /**
   * Add files and sub-directories in a folder to zip file.
   *
   * @param string     $folder
   *   Path to folder that should be zipped
   * @param ZipArchive $zipFile
   *   Zipfile where files end up
   * @param int        $exclusiveLength
   *   Number of text to be exclusived from the file path
   */
  private static function folderToZip($folder, &$zipFile, $exclusiveLength)
  {
      $handle = opendir($folder);
      while (false !== $f = readdir($handle)) {
          // Check for local/parent path or zipping file itself and skip.
      if ($f != '.' && $f != '..' && $f != basename(__FILE__)) {
          $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
          if (is_file($filePath)) {
              $zipFile->addFile($filePath, $localPath);
          } elseif (is_dir($filePath)) {
              // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
              self::folderToZip($filePath, $zipFile, $exclusiveLength);
          }
      }
      }
      closedir($handle);
  }
  /**
   * Zip a folder (including itself).
   * Usage:
   *   Zipper::zipDir('path/to/sourceDir', 'path/to/out.zip');.
   *
   * @param string $sourcePath
   *   Relative path of directory to be zipped
   * @param string $outZipPath
   *   Relative path of the resulting output zip file
   */
  public static function zipDir($sourcePath, $outZipPath)
  {
      $pathInfo = pathinfo($sourcePath);
      $parentPath = $pathInfo['dirname'];
      $dirName = $pathInfo['basename'];
      $z = new ZipArchive();
      $z->open($outZipPath, ZipArchive::CREATE);
      $z->addEmptyDir($dirName);
      if ($sourcePath == $dirName) {
          self::folderToZip($sourcePath, $z, 0);
      } else {
          self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
      }
      $z->close();
      $_SESSION['status'] = array('success' => 'Archiv erfolgreich erstellt: '.$outZipPath);
  }
}
?>
