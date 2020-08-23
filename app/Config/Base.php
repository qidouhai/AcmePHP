<?php

namespace App\Config;

class Base
{
    // 是否开启调试模式
    public static $debug = true;
    // 时区
    public static $timezone = 'Asia/Shanghai';
    // 是否支持跨域请求
    public static $isAllowCrossDomain = false;
    // 允许用户跨域列表，isAllowCrossDomain 字段为 true 时生效
    public static $accessControlAllowOrigin = [];
}
