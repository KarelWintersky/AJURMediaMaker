<?php

use AJURMediaMaker\App;

ini_set('memory_limit', '16G');
// define('PATH_CONFIG', '/etc/ajur/mediamaker/');
define('ENGINE_START_TIME', microtime(true));
if (!session_id()) @session_start();

require_once __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';
App::init($config);

try {
    $handle = new \Verot\Upload\Upload($_FILES['income_file']);

    if (!$handle->uploaded) {
        throw new RuntimeException("UPLOAD: file upload error: " . $handle->error);
    }

    $handle->file_new_name_body = getSystemUUID();
    $storage_path = config('path.storage.inbound');

    // файл нужно сохранить на диск под UUID-именем
    $handle->process($storage_path);
    if (!$handle->processed) {
        throw new RuntimeException("UPLOAD: file processing error: " . $handle->error);
    }
    $mimetype = $handle->file_src_mime;

    $process_properties = json_decode($_POST['properties'], true);

    // определить параметры логотипа из POST-данных
    // определить Mime-тип (для видео или фото разные параметры ffmpeg)
    // сформировать команду для обработки

    \AJURMediaMaker\Units\Convertor::convert($handle->file_dst_name, $mimetype, $process_properties);

    // выполнить команду
    // дождаться выполнения
    // сделать редирект на страницу download.php на которой нарисовать картинку и дать кнопку для скачивания
    // (причем путь к странице должен содержать UUID имя файла)
    // или не делать, а вывести шаблон?



} catch (RuntimeException $e) {
    dd($e);
}

$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();

    $render = \preg_replace('/^\h*\v+/m', '', $render); // удаляем лишние переводы строк

    echo $render;
}

