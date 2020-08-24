<?php

namespace App\Config;

class Routes
{
    // 默认控制器
    public static $defaultController = 'Home';
    // 默认方法
    public static $defaultMethod = 'index';
    // 默认错误控制器
    public static $defaultErrorController = 'Error';
    // 黑名单控制器
    public static $blacklistControllers = ['BaseController'];
    // 黑名单方法
    public static $blacklistMethods = ['model', 'helper'];
    // 路由表
    public static $routes = [];
}
