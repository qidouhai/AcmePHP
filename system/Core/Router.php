<?php

namespace NC\Core;

use App\Config\Routes as RoutesConfig;

class Router
{
    private static $pathinfo;
    private static $routingTable;
    private static $controller;
    private static $method;
    public static $params;

    public static function dispatch()
    {
        self::getPathInfo();
        self::parsingRoutes();
        self::parsingController();
        self::parsingMethod();
        self::parsingParams();
        $controllerClass = '\\App\\Controllers\\' . self::$controller;
        if (class_exists($controllerClass)) {
            self::request($controllerClass);
        } else {
            self::errorRequest();
        }
    }

    private static function getPathInfo()
    {
        self::$pathinfo = isset($_GET['route']) ? $_GET['route'] : die('路由匹配失败，请配置 Rewrite 规则');
    }

    private static function parsingRoutes()
    {
        $formatPath = '/' . implode('//', explode('/', trim(self::$pathinfo, '/'))) . '/';
        foreach (RoutesConfig::$routes as $key => $value) {
            $formatPath = str_replace('/' . trim($key, '/') . '/', '/' . trim($value, '/') . '/', $formatPath);
        }
        self::$routingTable = explode('/', str_replace('//', '/', trim($formatPath, '/')));
    }

    private static function request(string $controllerClass)
    {
        $controllerObj = new $controllerClass();
        $method = self::$method;
        if (method_exists($controllerObj,  $method)) {
            $controllerObj->$method('\NC\HTTP\Response', '\NC\HTTP\Request');
        } else {
            self::errorRequest();
        }
    }

    private static function errorRequest()
    {
        $errorControllerClass = '\\App\\Controllers\\' . RoutesConfig::$defaultErrorController;
        !class_exists($errorControllerClass) && die('错误控制器不存在，请创建错误控制器');
        $errorControllerObj = new $errorControllerClass();
        $defaultMethod = RoutesConfig::$defaultMethod;
        method_exists($errorControllerObj, $defaultMethod) || die('错误方法不存在，请创建错误方法');
        $errorControllerObj->$defaultMethod('\NC\HTTP\Response', '\NC\HTTP\Request');
    }

    private static function parsingController()
    {
        $controller = self::$routingTable[0] !== '' ? self::$routingTable[0] : RoutesConfig::$defaultController;
        $controllerArr = explode('-', $controller);
        array_walk($controllerArr, ['self', 'handlerController']);
        self::$controller = implode('', $controllerArr);
    }

    private static function handlerController(string &$controllerStr)
    {
        $controllerStr = ucfirst(strtolower($controllerStr));
    }

    private static function parsingMethod()
    {

        $method = isset(self::$routingTable[1]) ? self::$routingTable[1] : RoutesConfig::$defaultMethod;
        $methodArr = explode('-', $method);
        array_walk($methodArr, ['self', 'handlerMethod']);
        self::$method = implode('', $methodArr);
    }

    private static function handlerMethod(&$methodStr, $k)
    {
        if ($k > 0) {
            $methodStr = ucfirst(strtolower($methodStr));
        }
    }

    private static function parsingParams()
    {
        self::$params = isset(self::$routingTable[2]) ? array_slice(self::$routingTable, 2) : [];
    }
}
