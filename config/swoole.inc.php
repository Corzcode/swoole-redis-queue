<?php
/**
 * swoole配置
 */
return [
    'ip' => '0.0.0.0', 
    'port' => 9570, 
    'pack_type' => 'packet', 
    'mode' => SWOOLE_PROCESS, 
    'set' => [
        'user' => 'www', 
        'group' => 'www', 
        'worker_num' => 2, 
        'dispatch_mode' => 3, 
        'open_cpu_affinity' => true, 
        'open_tcp_nodelay' => true, 
        /*'package_eof' => "\r\n\r\n", /**/ 
        'package_max_length' => 1024 * 1024 * 2, 
        'daemonize' => false, 
        'log_file' => ROOT_PATH . 'logs/swoole_server.log']];