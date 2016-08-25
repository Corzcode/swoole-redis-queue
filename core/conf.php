<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 配置管理
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Conf
{

    /**
     * 配置
     * @var array
     */
    protected static $config = array();

    /**
     * 读取
     * 
     * @param string $name
     */
    public static function get($name)
    {
        strpos($name, '.') || $name = "config.$name";
        list ($file, $key) = explode('.', $name);
        if (! isset(self::$config[$file])) {
            self::load($file);
        }
        $config = self::$config[$file];
        return empty($key) ? $config : (isset($config[$key]) ? $config[$key] : null);
    }

    /**
     * 加速文件
     * 
     * @param string $file
     */
    protected static function load($file)
    {
        $path = CONF_PATH . $file . '.inc.php';
        if (file_exists($path)) {
            self::$config[$file] = include $path;
        }
    }
}