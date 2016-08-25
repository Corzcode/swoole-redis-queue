<?php

/*
 * PHP version 5.6
 */
define('CORE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
defined('CONF_PATH') or define('CONF_PATH', ROOT_PATH . 'config/');
defined('LOG_PATH') or define('LOG_PATH', ROOT_PATH . 'log/');

use Core\Error;
use Core\Log;
use Core\Server;
use Core\Debug;

/**
 * 初始化
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Init
{

    /**
     * 初始化
     */
    public static function start()
    {
        spl_autoload_register('self::autoload');
        Debug::$status = defined('DEBUG') ? DEBUG : false;
        Error::init();
        try {
            self::run();
        } catch (Exception $e) {
            //改由手动catch接管set_exception_handler
            echo "asdfasdf";
            Error::exception($e);
        }
    }

    /**
     * 运行控制器
     */
    public static function run()
    {
        Server::getInstance()->run();
    }

    /**
     * 自动加载类
     * 
     * @param string $class
     */
    public static function autoload($class)
    {
        if ('Controller' == substr($class, - 10)) {
            include APP_PATH . 'controller/' . strtolower(substr($class, 0, - 10)) . '.controller.php';
        } else {
            $namespace = '';
            $namepos = strrpos($class, '\\');
            if ($namepos) {
                $namespace = substr($class, 0, $namepos + 1);
                $class = substr($class, $namepos + 1);
                $namespace = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            }
            include ROOT_PATH . strtolower($namespace) . self::parseName($class) . '.php';
        }
    }

    /**
     * 转换命名风格
     *
     * @param string $name
     * @param int    $type
     * @return string
     */
    public static function parseName($name, $type = 0)
    {
        if ($type) {
            return ucfirst(
                preg_replace_callback('/_([a-zA-Z])/', 
                    function ($match) {
                        return strtoupper($match[1]);
                    }, $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }
}