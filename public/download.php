<?php

use AJURMediaMaker\App;
use AJURMediaMaker\Exceptions\FileNotFoundException;

define('ENGINE_START_TIME', microtime(true));
if (!session_id()) @session_start();

require_once __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';

App::init($config);

try {
    if (!isset($_GET['id'])) {
        throw new FileNotFoundException("Invalid URL");
    }

    $uuid = $_GET['id'];

    $f = new \AJURMediaMaker\Units\OutgoingFile($uuid);
    $filename = $f->fileinfo['fn_public'];
    $filename_star = \Normalizer::normalize($filename);

    $file_pointer = fopen($f->filepath, "rb");

    if ($file_pointer === false) {
        throw new FileNotFoundException("File not found or corrupt");
    }
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: {$f->fileinfo['mimetype']}");
    header("Content-Disposition: attachment; filename=\"{$filename}\";filename*=\"{$filename_star}\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: {$f->fileinfo['filesize']}");
    @ob_clean();
    rewind($file_pointer);
    fpassthru($file_pointer);

} catch (FileNotFoundException $e) {

} catch (RuntimeException $e) {

}