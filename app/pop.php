<?php

/*
 * PHP version 5.6
 */
namespace App;

use Core\App;
use Core\Debug;

/**
 * 出队控制
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Pop extends App
{

    public function run($data)
    {
        Debug::log($data);
        if (empty($data['queue'])) {
            throw new \Exception('param error');
        }
        $queue = $data['queue'];
        $block = isset($data['block']) ? $data['block'] : false;
        $redisObj = (yield $this->getRedis());
        if ($block === false) {
            $id = (yield $redisObj->rpoplpush("queue:$queue", "run:$queue"));
        } else {
            $id = (yield $redisObj->brpoplpush("queue:$queue", "run:$queue", $block));
        }
        Debug::log($id);
        $data = empty($id) ? null : (yield $redisObj->get("data:$queue:$id"));
        Debug::log($data);
        $r = $this->send(['id' => $id, 'data' => $data]);
        //如果客户端离线则推回数据
        if (! $r && $block !== false && ! empty($id)) {
            Debug::log("run:$queue, $id rollback");
            yield $redisObj->lpush("queue:$queue", $id);
            yield $redisObj->lrem("run:$queue", 1, $id);
        }
    }
}