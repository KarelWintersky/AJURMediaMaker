<?php

namespace AJURMediaMaker;

class NativeAPI
{
    /**
     * Возвращает информацию о файле по его 'file_id', используя нативный curl
     *
     * @param $file_id
     * @return mixed
     */
    public static function getFileInfo($file_id)
    {
        $token = App::$bot_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . $token . '/getFile');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'file_id=' . $file_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    /**
     * Скачивает файл или в бинарный поток или в файл
     *
     * @param $file_id
     * @param $target_filename
     * @return bool|string
     */
    public static function downloadFile($file_id, $target_filename = null)
    {
        $token = App::$bot_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . $token . '/getFile');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'file_id=' . $file_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($ch);

        $json = json_decode($result, true);

        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/file/bot' . $token . '/' . $json['result']['file_path']);
        curl_setopt($ch, CURLOPT_POST, FALSE);

        if (!empty($target_filename)) {
            $file = fopen($target_filename, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $file); // Указываем, что вывод нужно записывать в файл
            curl_exec($ch);
            fclose($file);
            $result = '';
        } else {
            $result = curl_exec($ch);
        }
        curl_close($ch);;
        return $result;
    }

    public static function sendMessage($chat_id = '', $message = '', $reply_markup = [])
    {
        if (empty($chat_id) || empty($message)) {
            return false;
        }

        $token = App::$bot_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            'https://api.telegram.org/bot' . $token . '/sendMessage');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'chat_id=' . $chat_id . '&text=' . rawurlencode($message) .
            '&reply_markup=' . json_encode($reply_markup));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        return curl_exec($ch);
    }

}