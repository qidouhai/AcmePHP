<?php

namespace App\Helper;

class Captcha
{
    private $width;
    private $height;
    private $verifyNum;
    private $verifyType;
    private $verifyCode;
    private $image;

    /**
     * @param integer $width 宽度
     * @param integer $height 高度
     * @param integer $verifyNum 字符数量
     * @param integer $verifyType 类型 1 数字 2 字母 3 数字加字母
     */
    public function __construct(int $width = 100, int $height = 36, int $verifyNum = 4, int $verifyType = 3)
    {
        $this->width = $width;
        $this->height = $height;
        $this->verifyNum =  $verifyNum;
        $this->verifyType = $verifyType;
        $this->verifyCode = $this->createVerifyCode();
    }

    public function show()
    {
        $this->createImage();
        $this->setDisturbColor();
        $this->outputText();
        $this->outputImage();
        $this->destruct();
    }

    private function fontColor()
    {
        return imagecolorallocate($this->image, mt_rand(30, 60), mt_rand(30, 60), mt_rand(30, 60));
    }

    private function darkColor()
    {
        return imagecolorallocate($this->image, mt_rand(80, 120), mt_rand(80, 120), mt_rand(80, 120));
    }

    private function lightColor()
    {
        return imagecolorallocate($this->image, mt_rand(160, 200), mt_rand(160, 200), mt_rand(160, 200));
    }

    private function createImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        imagefill($this->image, 0, 0, $this->lightColor());
    }

    private function setDisturbColor()
    {
        $num = ceil($this->width * $this->height / 20);
        for ($i = 0; $i < $num; $i++) {
            imagesetpixel($this->image, mt_rand(0, $this->width), mt_rand(0, $this->height), $this->darkColor());
        }
        for ($i = 0; $i < 20; $i++) {
            imagearc(
                $this->image,
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                mt_rand(0, 90),
                mt_rand(90, 180),
                $this->darkColor()
            );
        }
    }

    private function createVerifyCode()
    {
        switch ($this->verifyType) {
            case 1:
                $str = implode('', array_rand(range(0, 9), $this->verifyNum));
                break;
            case 2:
                $str = implode('', array_rand(array_flip(array_merge(range('a', 'z'), range('A', 'Z'))), $this->verifyNum));
                break;
            case 3:
                $words = str_shuffle('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789');
                $str = substr($words, 0, $this->verifyNum);
                break;
        }
        return $str;
    }

    private function outputText()
    {
        $every = ceil(($this->width) / $this->verifyNum);
        for ($i = 0; $i < $this->verifyNum; $i++) {
            $x = mt_rand($every * $i + 5, $every * ($i + 1) - 15);
            $y = mt_rand(5, $this->height - 20);
            imagechar($this->image, 5, $x, $y, $this->verifyCode[$i], $this->fontColor());
        }
    }

    private function outputImage()
    {
        header('Content-Type: image/png');
        imagepng($this->image);
    }

    private function destruct()
    {
        imagedestroy($this->image);
    }

    public function __get($name)
    {
        if ($name === 'verifyCode') {
            return $this->verifyCode;
        }
    }
}
