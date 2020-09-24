<?php

namespace NC\HTTP;

use NC\Core\Router;

class Request
{
    /**
     * 获取路径参数
     */
    public static function pathParams()
    {
        return Router::$params;
    }

    /**
     * 获取 POST JSON 参数
     */
    public static function postMethodJSONParams()
    {
        $params = json_decode(file_get_contents("php://input"), true);
        $params = $params ? $params : [];
        array_walk($params, ['self', 'trim']);
        return $params;
    }

    private static function trim(&$v)
    {
        $v = trim($v);
    }

    /**
     * 获取 POST 表单参数
     */
    public static function postMethodParams()
    {
        return $_POST;
    }

    /**
     * 获取 GET 参数
     */
    public static function getMethodParams()
    {
        return $_GET;
    }
}
