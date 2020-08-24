<?php

namespace NC;

use App\Config\Base as BaseConfig;
use NC\Core\Router;

class App
{
    public static function initialize()
    {
        BaseConfig::$debug || error_reporting(0);
        define('START_RUN_TIME', microtime(true));
        session_save_path(ROOT_PATH . 'writable/session');
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