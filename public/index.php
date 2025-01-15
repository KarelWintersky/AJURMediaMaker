<?php
/**
 * Универсальная fallback-страница
 */

// define('PATH_CONFIG', '/etc/ajur/mediamaker/');
define('ENGINE_START_TIME', microtime(true));
if (!session_id()) @session_start();

use AJURMediaMaker\App;

require_once __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';
App::init($config);

App::$template->assign("max_upload_size", config('upload_limits.REAL_MAX_SIZE'));
App::$template->setTemplate("index.tpl");

$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();

    $render = \preg_replace('/^\h*\v+/m', '', $render); // удаляем лишние переводы строк

    echo $render;
}
