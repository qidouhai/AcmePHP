<?php
$minPHPVersion = 7;
if (phpversion() < $minPHPVersion) {
    die("您的 PHP 版本必须为 {$minPHPVersion} 或更高版本。当前版本：" . phpversion());
}
unset($minPHPVersion);
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
require ROOT_PATH . 'system/Autoloader/autoload.php';
Acme\App::run();
