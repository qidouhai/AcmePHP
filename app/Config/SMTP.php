<?php

namespace App\Config;

class SMTP
{
    // SMTP 服务器
    public static $host = 'smtp.qq.com';
    // 用户名
    public static $username = 'admin@qq.com';
    // 昵称
    public static $nickname = 'NCPHP';
    // 密码
    public static $password = '';
    // 允许 TLS 或者 SSL 协议
    public static $secure = 'ssl';
    // 服务器端口 25 或者 465
    public static $port = '465';
}
