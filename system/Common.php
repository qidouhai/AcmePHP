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

/**
 * 导入视图
 * @param string|array $view 视图
 * @param array $data 数据
 * @param callback $funcname 回调函数
 */
function include_view($view, array $data = [], $funcname = '')
{
    ob_start();
    extract($data);
    if (is_array($view)) {
        foreach ($view as $value) {
            $viewPath = ROOT_PATH . 'app/Views/' . trim($value, '/') . '.php';
            file_exists($viewPath) && include $viewPath;
        }
    } else {
        $viewPath = ROOT_PATH . 'app/Views/' . trim($view, '/') . '.php';
        file_exists($viewPath) && include $viewPath;
    }
    $html = ob_get_contents();
    ob_end_clean();
    echo $html;
    if (!empty($funcname)) {
        call_user_func_array($funcname, []);
    }
}
