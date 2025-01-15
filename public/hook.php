<?php

use AJURMediaMaker\App;
use AJURMediaMaker\NativeAPI;

require_once __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';

App::init($config);

$bot_username = $config['bot']['name'];
$bot_api_key  = $config['bot']['token'];

// main
$data = input();

$chat_id = $data['message']['chat']['id'];

// write log
if (!empty($data['message']['photo'])) {
    foreach ($data['message']['photo'] as $i => $p) {
        $fileinfo = NativeAPI::getFileInfo($p['file_id']);

        $data['message']['photo'][$i]['file_size'] = $fileinfo['result']['file_size'];
        $data['message']['photo'][$i]['file_path'] = $fileinfo['result']['file_path'];
    }
}

if (!empty($data['message']['video'])) {
    $video = $data['message']['video'];

    $dir = App::$config->get('app.logs');
    $fn = $dir . "/" . $video['file_name'];

    NativeAPI::downloadFile($video['file_id'], $fn);

    NativeAPI::sendMessage($data['message']['chat']['id'] ?? '', "Video downloaded");
}

App::log($data);

die;

if (!empty($data['message']['text'])) {
    $chat_id = $data['message']['from']['id'];
    $user_name = $data['message']['from']['username'];
    $first_name = $data['message']['from']['first_name'];
    $last_name = $data['message']['from']['last_name'];
    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);

//    $state = States::getUserState($chat_id);
}







/*// старт (?) или перезапуск бота
if (
    isset($data['my_chat_member']) ||
    (isset($data['message']['text']) && $data['message']['text'] == '/start')
) {
    // установить для пользователя состояние 'welcome'
    $userId = $data['my_chat_member']['chat']['id'] ?? $data['message']['chat']['id'];

    States::setUserState($userId, 'start');

}

$userId = $data['my_chat_member']['chat']['id'] ?? $data['message']['chat']['id'];
$userState = States::getUserState($userId);

if (!$userState) {
    // Если состояние не найдено, устанавливаем начальное состояние
    States::setUserState($userId, 'start');
    $reply = "Вы запустили бот обработки медиаресурсов АЖУР-Медиа/47news, загрузите изображение:";
    $buttons = [
        [ "text" => "Фонтанка", "callback_data" => "/mode_fontanka"],
        [ "text" => "47news",   "callback_data" => "/mode_47news"],
    ];
} else {
    switch ($userState['state']) {
        case 'start': {
            break;
        }
    }
}*/





/*if (!$userState) {


    $reply = "Привет! Как я могу помочь?";
} else {
    // Обработка на основе текущего состояния
    switch ($userState['state']) {
        case 'start':
            // С команды /start начинается работа бота
            States::setUserState($userId, 'next_state');
            $reply = "Вы находитесь в начальном состоянии. Какой следующий шаг?";
            break;
        case 'next_state':
            // Логика для состояния 'next_state'
            States::setUserState($userId, 'end');
            $reply = "Вы завершили процесс!";
            break;
        // Добавьте другие состояния по мере необходимости
    }
}*/

// send message to TG

// file_get_contents("https://api.telegram.org/bot{$bot_api_key}/sendMessage?chat_id={$userId}&text=" . urlencode($reply));


