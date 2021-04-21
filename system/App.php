<?php

namespace Acme;

use App\Config\Base as BaseConfig;
use Acme\Core\Router;

class App
{
    public static function initialize()
    {
        BaseConfig::$debug || error_reporting(0);
        define('START_RUN_TIME', microtime(true));
        file_exists(ROOT_PATH . 'writable/session/') || mkdir(ROOT_PATH . 'writable/session/', 0777, true);
        session_save_path(ROOT_PATH . 'writable/session/');
        session_start();
        date_default_timezone_set(BaseConfig::$timezone);
        if (BaseConfig::$isAllowCrossDomain) {
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
            if (in_array($origin, BaseConfig::$accessControlAllowOrigin)) {
                header("Access-Control-Allow-Origin: {$origin}");
                header('Access-Control-Allow-Credentials: true');
            }
        }
    }

    public static function run()
    {
        self::initialize();
        Router::dispatch();
    }
}
