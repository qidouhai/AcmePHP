<?php

namespace Acme;

class Controller
{
    /**
     * 获取模型实例
     * @param string $className 模型类名
     */
    protected function model(string $className)
    {
        $modelClass = '\\App\\Models\\' . $className;
        $this->$className = class_exists($modelClass) ? new $modelClass() : die("模型类 {$className} 不存在");
    }

    /**
     * 获取工具类实例
     * @param string $className 工具类名
     * @param array $initParams 初始化参数
     */
    protected function helper(string $className, array $initParams = [])
    {
        $helperClass = '\\App\\Helper\\' . $className;
        $this->$className = class_exists($helperClass) ? new $helperClass(...$initParams) : die("工具类 {$className} 不存在");
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
