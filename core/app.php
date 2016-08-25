<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 应用开始
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
abstract class App
{

    /**
     * 请求对象
     * @var Requester
     */
    protected $requester = null;

    /**
     * 请求对象
     * @var Redis
     */
    protected $redis = null;

    /**
     *  构造
     * 
     * @param Requester $requester
     */
    public function __construct(Requester $requester = null)
    {
        $this->requester = $requester;
        Debug::log(static::class . '::__construct');
    }

    /**
     * 发送数据
     * 
     * @param string $data
     * @return boolean
     */
    public function send($data)
    {
        if (is_array($data)) {
            $data['status'] = 200;
            $data = json_encode($data);
        }
        return $this->requester->send($data);
    }

    /**
     * 执行任务
     */
    abstract public function run($data);

    /**
     * 获取redis实例
     * 
     * @return Redis
     */
    public function getRedis()
    {
        $this->redis = Redis::getInstance(false);
        return $this->redis->connect();
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        if ($this->redis) {
            Redis::releaseInstance($this->redis);
        }
        Debug::log(static::class . '::__destruct');
    }
}