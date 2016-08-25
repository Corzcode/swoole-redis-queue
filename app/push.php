<?php

/*
 * PHP version 5.6
 */
namespace App;

use Core\App;
use Core\Debug;

/**
 * 入队控制
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Push extends App
{

    public function run($data)
    {
        Debug::log($data);
        $id = $this->buildId();
        if (empty($data['queue']) || empty($data['data'])) {
            throw new \Exception('param error');
        }
        $queue = $data['queue'];
        $redisObj = (yield $this->getRedis());
        $data = (yield $redisObj->set("data:$queue:$id", $data['data']));
        Debug::log($data);
        $data = (yield $redisObj->lpush("queue:$queue", $id));
        Debug::log($data);
        $this->send(['id' => $id]);
    }

    /**
     * 生成任务id
     * 
     * @return string
     */
    protected function buildId()
    {
        static $i = 0, $pid = 0, $ip = '';
        $i ++;
        if (empty($pid)) {
            $pid = posix_getpid();
            $ip = implode(',', swoole_get_local_ip());
        }
        return md5("$ip-$pid-$i-" . microtime(true));
    }
}