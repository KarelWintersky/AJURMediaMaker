<?php

// https://nutgram.dev/docs/configuration/installation

use AJURMediaMaker\App;
use AJURMediaMaker\States;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\MessageType;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

require_once __DIR__ . '/../vendor/autoload.php';
$config = include __DIR__ . '/../config.php';

App::init($config);

try {
    $bot = new Nutgram(App::$bot_token);
    $bot->setRunningMode(\SergiX44\Nutgram\RunningMode\Webhook::class);

    $bot->onCommand('start', function (Nutgram $bot){
        $userId = $bot->userId();
        States::setUserState($userId, 'start');
        App::log($bot);

        $bot->sendMessage(
            text: 'Вы вышли в основное меню',
            reply_markup: ReplyKeyboardMarkup::make()->addRow(
                KeyboardButton::make('Give me food!'),
                KeyboardButton::make('Give me animal!'),
            )
        );

    });

    $bot->onMessageType(MessageType::PHOTO, function (Nutgram $bot){
        $photo = end($bot->message()->photo);

        // $bot->

        // $bot->getFile()

        App::log($photo, 'photo');
        $bot->sendMessage("Photo recieved: ");
    });

    // $bot->onMessageType()

    /*$bot->onPhoto(function (Nutgram $bot) {
        $bot->message()->photo

        App::log($bot->message()->getType(), 'type');
        App::log($bot->pro);
        // $bot->getFile()->save()
    });*/

    $bot->run();

} catch (Exception|Throwable $e) {
    App::log($e, 'error');
}


