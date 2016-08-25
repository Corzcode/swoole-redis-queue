<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 错误控制
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Error
{

    /**
     * 错误代码
     * @var array
     */
    protected static $strMap = array(
        E_ERROR => 'E_ERROR', 
        E_WARNING => 'E_WARNING', 
        E_PARSE => 'E_PARSE', 
        E_NOTICE => 'E_NOTICE', 
        E_CORE_ERROR => 'E_CORE_ERROR', 
        E_CORE_WARNING => 'E_CORE_WARNING', 
        E_COMPILE_ERROR => 'E_COMPILE_ERROR', 
        E_COMPILE_WARNING => 'E_COMPILE_WARNING', 
        E_USER_ERROR => 'E_USER_ERROR', 
        E_USER_NOTICE => 'E_USER_NOTICE', 
        E_USER_WARNING => 'E_USER_WARNING', 
        E_USER_DEPRECATED => 'E_USER_DEPRECATED', 
        E_STRICT => 'E_STRICT', 
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR', 
        E_DEPRECATED => 'E_DEPRECATED', 
        E_USER_DEPRECATED => 'E_USER_DEPRECATED');

    /**
     * 初始化
     */
    public static function init()
    {
        register_shutdown_function('\Core\Error::shutdown');
        set_error_handler('\Core\Error::handler');
        //set_exception_handler('Error::exception');
    }

    /**
     * 致命错误接口
     */
    public static function shutdown()
    {
        $e = error_get_last();
        if (! empty($e)) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    //ob_end_clean();
                    self::handler($e['type'], $e["message"], $e["file"], $e["line"]);
                    //Init::httpStatus(500);
                    //Init::output(500, $e["message"]);
                    break;
            }
        }
    }

    /**
     * 错误接口
     * 
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param int    $errno
     */
    public static function handler($errno, $errstr, $errfile, $errline)
    {
        if (is_int($errno)) {
            if (! self::checkLevel($errno)) {
                return true;
            }
            $errno = self::errStr($errno);
        }
        $strMsg = 'DATE:' . date('Y-m-d H:i:s') . "     URL:\n";
        $strMsg .= 'FILE:' . $errfile . ' LINE:' . $errline . "\n";
        $strMsg .= $errno . ':' . $errstr . "\n";
        Log::record($strMsg, 'error');
    }

    /**
     * 异常接口
     * 
     * @param Exception $e
     */
    public static function exception(\Exception $e)
    {
        if ('LogicException' != get_class($e)) {
            self::handler('E_EXCEPTION', $e->getMessage(), $e->getFile(), $e->getLine());
        }
        //Init::output($e->getCode() ?: 500, $e->getMessage());
    }

    /**
     * 错误信息
     * @param int $code
     * @return string
     */
    public static function errStr($code)
    {
        $str = self::$strMap[$code];
        return $str;
    }

    /**
     * 获取错误日志可记录配置信息
     *
     * @param   int $errno
     * @return  boolean
     */
    private static function checkLevel($errno)
    {
        static $logLevel = null;
        isset($logLevel) || $logLevel = Conf::get('error_log_level');
        return ($errno & $logLevel) == $errno;
    }
}