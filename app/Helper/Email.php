<?php

namespace App\Helper;

use Exception;
use App\Helper\Library\PHPMailer\PHPMailer;
use App\Config\SMTP as SMTPConfig;

class Email
{
    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->CharSet = "UTF-8";
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = SMTPConfig::$host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTPConfig::$username;
        $this->mail->Password = SMTPConfig::$password;
        $this->mail->SMTPSecure = SMTPConfig::$secure;
        $this->mail->Port = SMTPConfig::$port;
    }

    private function addAttachment(array $v)
    {
        if (isset($v['path']) && isset($v['name'])) {
            $this->mail->AddAttachment($v['path'], $v['name']);
        }
    }

    /**
     * 发送
     * @param string|array $receiver 收件人，支持多个
     * @param string $subject 标题
     * @param string $body 内容
     * @param array $attachment 附件，支持多个
     */
    public function send($receiver, string $subject, string $body, array $attachment = [])
    {
        try {
            $this->mail->setFrom(SMTPConfig::$username, SMTPConfig::$nickname);
            if (is_array($receiver)) {
                array_walk($receiver, function ($v) {
                    $this->mail->addAddress($v);
                });
            } else {
                $this->mail->addAddress($receiver);
            }
            $this->mail->addReplyTo(SMTPConfig::$username, SMTPConfig::$nickname);
            if (count($attachment) === count($attachment, 1)) {
                $this->AddAttachment($attachment);
            } else {
                array_walk($attachment, [$this, 'AddAttachment']);
            }
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = $body;
            $this->mail->send();
            return [
                'code' => 0
            ];
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg' => $this->mail->ErrorInfo
            ];
        }
    }
}
