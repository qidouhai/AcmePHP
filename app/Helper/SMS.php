<?php

namespace App\Helper;

use App\Config\SMS as SMSConfig;
use App\Helper\Library\SignatureHelper\SignatureHelper;

class SMS
{
    private $accessKeyId;
    private $accessKeySecret;

    public function __construct()
    {
        $this->accessKeyId = SMSConfig::$accessKeyId;
        $this->accessKeySecret = SMSConfig::$accessKeySecret;
    }

    /**
     * 发送短信
     * @param string|array $phoneNumbers 发送电话号码，支持批量发送
     * @param array $templateParam 短信模板参数
     * @param string $outId 流水号
     */
    public function sendSms($phoneNumbers, array $templateParam = [], string $outId = '')
    {
        $params = [];
        $security = SMSConfig::$security;
        $params['PhoneNumbers'] = is_array($phoneNumbers) ? implode(',', $phoneNumbers) : $phoneNumbers;
        $params['SignName'] = SMSConfig::$signName;
        $params['TemplateCode'] = SMSConfig::$templateCode;
        $params['TemplateParam'] = json_encode($templateParam, JSON_UNESCAPED_UNICODE);
        if (!empty($OutId)) {
            $params['OutId'] = $outId;
        }
        $helper = new SignatureHelper();
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            'dysmsapi.aliyuncs.com',
            array_merge($params, [
                'RegionId' => 'cn-hangzhou',
                'Action' => 'SendSms',
                'Version' => '2017-05-25'
            ]),
            $security
        );
        $content = json_decode(json_encode($content), true);
        if ($content['Code'] === 'OK') {
            return [
                'code' => 0,
                'content' => $content
            ];
        } else {
            return [
                'code' => -1,
                'content' => $content
            ];
        }
    }

    /**
     * 查询发送记录
     * @param string $phoneNumber 发送电话号码
     * @param string $sendDate 短信发送日期，格式 yyyyMMdd，支持近 30 天记录查询
     * @param string $pageSize 分页大小
     * @param string $currentPage 当前页码
     * @param string $bizId 设置发送短信流水号
     */
    public function querySendDetails(string $phoneNumber, string $sendDate, int $pageSize = 20, int $currentPage = 1, string $bizId = '')
    {
        $params = [];
        $security = SMSConfig::$security;
        $params["PhoneNumber"] = $phoneNumber;
        $params["SendDate"] = $sendDate;
        $params["PageSize"] = $pageSize;
        $params["CurrentPage"] = $currentPage;
        if (!empty($bizId)) {
            $params["BizId"] = $bizId;
        }
        $helper = new SignatureHelper();
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, [
                'RegionId' => 'cn-hangzhou',
                'Action' => 'QuerySendDetails',
                'Version' => '2017-05-25'
            ]),
            $security
        );
        $content = json_decode(json_encode($content), true);
        if ($content['Code'] === 'OK') {
            return [
                'code' => 0,
                'content' => $content
            ];
        } else {
            return [
                'code' => -1,
                'content' => $content
            ];
        }
    }
}
