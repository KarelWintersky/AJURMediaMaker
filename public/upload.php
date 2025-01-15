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

    $handle->file_new_name_body = $uuid = getSystemUUID();
    $storage_path = config('path.storage.inbound');

    // файл нужно сохранить на диск под UUID-именем
    $handle->process($storage_path);
    if (!$handle->processed) {
        throw new RuntimeException("UPLOAD: file processing error: " . $handle->error);
    }

    // определить Mime-тип (для видео или фото разные параметры ffmpeg)
    $mimetype = $handle->file_src_mime;
    $is_image = str_contains($mimetype, 'image/');
    $is_video = str_contains($mimetype, 'video/');

    // определить параметры логотипа из POST-данных
    $process_properties = json_decode($_POST['properties'], true);

    // сформировать команду для обработки
    // выполнить команду
    // дождаться выполнения
    $r = \AJURMediaMaker\Units\Convertor::convert($handle->file_dst_name, $mimetype, $process_properties);

    // рисуем из шаблона страницу с картинкой и кнопкой скачивания
    App::$template->assign('error', 0);
    App::$template->assign('uuid', $uuid);
    App::$template->assign('dest_file', $handle->file_dst_name );
    App::$template->assign("dest_ext", $is_image ? 'jpg' : ($is_video ? 'mp4' : 'dat'));
    App::$template->assign("is_image", $is_image);
    App::$template->assign("is_video", $is_video);
    App::$template->assign("domain", config('url.site'));
    App::$template->setTemplate("result.tpl");

} catch (RuntimeException $e) {
    App::$template->assign("error", $e->getCode());
    App::$template->assign("error_message", $e->getMessage());
}

$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();

    $render = \preg_replace('/^\h*\v+/m', '', $render); // удаляем лишние переводы строк

    echo $render;
}

