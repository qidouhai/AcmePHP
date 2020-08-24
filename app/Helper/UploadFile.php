<?php

namespace App\Helper;

class UploadFile
{
    private $key;
    private $allowedExts;
    private $maxSize;
    private $extension;
    private $savepath;
    private $savename;

    /**
     * @param string $key File key
     * @param array $allowedExts 允许文件后缀
     * @param string $maxSize 最大上传大小，单位 kB
     */
    public function __construct(string $key, array $allowedExts, string $maxSize)
    {
        $this->key = $key;
        $this->allowedExts = $allowedExts;
        $this->maxSize = $maxSize;
    }

    /**
     * 文件上传
     */
    public function upload()
    {
        $result = $this->limit();
        if ($result['code'] !== 0) {
            return $result;
        } else {
            return $this->saveFile();
        }
    }

    private function limit()
    {
        if (!isset($_FILES[$this->key])) {
            return ['code' => 1];
        }
        $temp = explode('.', $_FILES[$this->key]['name']);
        $this->extension = end($temp);
        if (!in_array($this->extension, $this->allowedExts)) {
            return ['code' => 2];
        }
        if ($_FILES[$this->key]['size'] > $this->maxSize * 1024) {
            return ['code' => 3];
        }
        $this->savepath = ROOT_PATH . 'writable/uploads/';
        $this->savename = create_guid() . ".{$this->extension}";

        if (file_exists("{$this->savepath}{$this->savename}")) {
            return ['code' => 4];
        }
        return ['code' => 0];
    }

    private function saveFile()
    {
        if (@move_uploaded_file($_FILES[$this->key]['tmp_name'], "{$this->savepath}{$this->savename}")) {
            return [
                'code' => 0,
                'filename' => $this->savename,
            ];
        } else {
            return [
                'code' => -1
            ];
        }
    }
}
