<?php
class ClassLoader
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance instanceof ClassLoader) {
            self::$instance = new ClassLoader();
        }
        return self::$instance;
    }

    private function findFile(string $class)
    {
        $class = explode('\\', $class);
        $class[0] = $class[0] === 'Acme' ? 'system' : lcfirst($class[0]);
        return ROOT_PATH . implode('/', $class) . '.php';
    }

    private function loadClass(string $class)
    {
        $file = $this->findFile($class);
        if (file_exists($file)) {
            include_file($file);
            return true;
        }
        return false;
    }

    public function register()
    {
        spl_autoload_register([$this, 'loadClass'], true, true);
    }
}

function include_file($file)
{
    include $file;
}
