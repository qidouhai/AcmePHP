<?php

namespace App\Config;

class SMS
{
    // 阿里云 accessKeyId
    public static $accessKeyId = '';
    // 阿里云 accessKeySecret
    public static $accessKeySecret = '';
    // 是否启用 https
    public static $security = true;
    // 短信签名
    public static $signName = '';
    // 短信模板 Code
    public static $templateCode = '';
}
