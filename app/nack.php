<?php

/*
 * PHP version 5.6
 */
namespace App;

use Core\App;
use Core\Debug;

/**
 * 确认消息失败 放回队列
 *
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Nack extends App
{

    public function run($data)
    {
        if (empty($data['queue']) || empty($data['id'])) {
            throw new \Exception('param error');
        }
        $queue = $data['queue'];
        $id = $data['id'];
        $redisObj = (yield $this->getRedis());
        Debug::log("Nack:$queue, $id");
        $r = (yield $redisObj->lrem("run:$queue", 1, $id));
        Debug::log($r);
        $r = (yield $redisObj->lpush("queue:$queue", $id));
        Debug::log($r);
        $this->send($data);
    }
}