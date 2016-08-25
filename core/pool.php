<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 对象池
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Pool
{

    /**
     * @var array
     */
    protected static $pool = array();

    /**
     * 获取实例
     * 
     * @param string $className
     * @return mixed
     */
    public static function getInstance($className)
    {
        if (! isset(self::$pool[$className])) {
            self::$pool[$className] = new \SplQueue();
        }
        if (self::$pool[$className]->isEmpty()) {
            return new $className();
        } else {
            return self::$pool[$className]->shift();
        }
    }

    /**
     * 释放实例
     * 
     * @param mixed $instance
     */
    public static function releaseInstance($instance)
    {
        $className = get_class($instance);
        self::$pool[$className]->push($instance);
    }
}