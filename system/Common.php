<?php

/**
 * 获取路由
 * @param integer $level 层级
 */
function route_path(int $level = -1)
{
    $routeStr = trim($_GET['route'], '/');
    return $level === -1 ? $routeStr : implode('/', array_slice(explode('/', $routeStr), 0, $level));
}

/**
 * 时间戳处理
 * @param integer $timestamp 时间戳
 */
function date_process(int $timestamp)
{
    return date('Y-m-d H:i:s', $timestamp);
}

/**
 * 获取客户端 IP
 */
function client_ip()
{
    return ip2long($_SERVER['REMOTE_ADDR']);
}

/**
 * 创建唯一 ID
 */
function create_guid()
{
    return md5(uniqid());
}

/**
 * 创建验证 code
 * @param integer $length 长度
 */
function create_code(int $length = 6)
{
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $char = substr('1234567890', rand(0, 9), 1);
        $code .= $char;
    }
    return $code;
}
