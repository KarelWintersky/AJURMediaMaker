<?php

namespace AJURMediaMaker;

use Arris\AppLogger;
use Arris\Core\Dot;
use Arris\Database\DBWrapper;
use Arris\Template\FlashMessages;
use Arris\Template\Template;
use PDO;

class App extends \Arris\App
{
    public static DBWrapper|PDO $pdo;

    public static Dot $config;

    public static string $bot_name;

    public static string $bot_token;

    /**
     * @var Template
     */
    public static Template $template;

    /**
     * @var FlashMessages
     */
    public static FlashMessages $flash;

    public static function init(array $config)
    {
        self::$config = new Dot($config);

        App::factory(self::$config);

        self::$pdo = new \Arris\Database\DBWrapper([
            'database'  =>  $config['db']['database'],
            'username'  =>  $config['db']['username'],
            'password'  =>  $config['db']['password'],
            'charset'   =>  "utf8mb4",
            'charset_collate'   =>  "utf8mb4_unicode_ci"
        ]);

        self::$bot_name = $config['bot']['name'];
        self::$bot_token  = $config['bot']['token'];

        self::$flash = new \Arris\Template\FlashMessages();

        self::$template = new Template();
        self::$template
            ->setTemplateDir(config('path.templates'))
            ->setCompileDir(config('path.cache'))
            ->setForceCompile(true)
            ;

        self::$template->assign("flash_messages", App::$flash->getMessage('flash', []));

        AppLogger::init('ajur-media-maker', bin2hex(random_bytes(8)), [
            'default_logfile_path'  =>  config('path.logs') . DIRECTORY_SEPARATOR
        ]);
    }

    public static function log($data, $mode = 'debug')
    {
        $dir = self::$config->get('app.logs');
        $fn = $dir . "/{$mode}-" . date('Y-m-d-H-i-s-u') . '.txt';
        file_put_contents($fn , print_r($data, true));
    }


}