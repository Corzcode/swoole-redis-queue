<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 日志记录
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Log
{

    public static function record($message, $fileName = '')
    {
        $logpath = Conf::get('log_path');
        $dir = $logpath;
        if (! empty($fileName)) {
            $dir = $logpath . '/' . $fileName;
            if (! file_exists($dir) && false === mkdir($dir)) {
                throw new \Exception('log directory is not to be written', 500);
            }
        }
        $dirdate = date(Conf::get('log_file_format')) . '.log';
        $dir .= '/' . $dirdate;
        error_log($message . "\n", 3, $dir);
    }
}
