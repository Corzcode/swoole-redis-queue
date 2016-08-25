<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * Debug信息
 *
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Debug
{

    public static $status = false;

    /**
     * 记录日志
     * 
     * @param string $msg
     */
    public static function log()
    {
        if (self::$status) {
            $msg = '';
            foreach (func_get_args() as $value) {
                $msg .= ' ';
                if (is_array($value)) {
                    $msg .= var_export($value, true);
                } elseif (is_bool($value)) {
                    $msg .= $value ? 'true' : 'false';
                } elseif (is_object($value)) {
                    $msg .= get_class($value) . '[' . spl_object_hash($value) . ']';
                } elseif (is_null($value)) {
                    $msg .= 'NULL';
                } else {
                    $msg .= $value;
                }
            }
            $msg = substr($msg, 1);
            echo $msg, "\n";
        }
    }
}