<?php

namespace NC\HTTP;

class Response
{
    /**
     * 模板响应
     * @param string|array $view 视图
     * @param array $data 数据
     * @param callback $funcname 回调函数
     */
    public static function view($view, $data = [], $funcname = '')
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
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        if (!empty($funcname)) {
            call_user_func_array($funcname, []);
        }
        exit;
    }

    /**
     * 响应 json
     * @param string|array $data 数据
     * @param string $msg 信息
     * @param integer $code 响应码
     * @param callback $funcname 回调函数
     */
    public static function json($data, $msg = '成功', $code = 0, $funcname = '')
    {
        header("Content-Type:application/json; charset=utf-8");
        echo json_encode(['data' => $data, 'msg' => $msg, 'code' => $code], JSON_UNESCAPED_UNICODE);
        if (!empty($funcname)) {
            call_user_func_array($funcname, []);
        }
        exit;
    }
}
