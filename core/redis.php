<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 异步redis
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Redis
{

    /**
     * @var \swoole_redis
     */
    protected $redis = null;

    /**
     * @var callable
     */
    protected $callback = null;

    /**
     * 获取实例
     *
     * @return self
     */
    public static function getInstance($autoConnect = true)
    {
        Debug::log(__METHOD__);
        $instance = Pool::getInstance(self::class);
        return $autoConnect ? $this->connect() : $instance;
    }

    /**
     * 释放实例
     *
     * @return self
     */
    public static function releaseInstance(Redis $instance)
    {
        Debug::log(__METHOD__);
        Pool::releaseInstance($instance);
    }

    /**
     * 构造
     */
    public function __construct()
    {
        Debug::log(__METHOD__);
    }

    /**
     * 连接
     * 
     * @param callable $callback
     */
    public function connect(callable $callback = null)
    {
        if (empty($callback)) {
            return new Task([$this, 'connect']);
        } elseif ($this->redis) {
            $callback($this);
        } else {
            $this->redis = new \swoole_redis();
            $this->callback = $callback;
            $this->redis->on('close', 
                function () {
                    Debug::log('redis close');
                    $this->redis = null;
                });
            $this->redis->on('message', function ($r) {
                Debug::log("redis message : $r");
            });
            $conf = Conf::get('redis');
            $this->redis->connect($conf['ip'], $conf['port'], 
                function (\swoole_redis $client, $result) {
                    $callback = $this->callback;
                    $this->callback = null;
                    $callback($this);
                });
        }
    }

    /**
     * 执行coro
     * 
     * @param callable $callback
     * @param string $func
     * @param array  $argv
     */
    public function run(callable $callback, $func, array $argv)
    {
        $this->callback = $callback;
        Debug::log(__METHOD__ . " -> $func");
        $argv[] = [$this, 'callback'];
        call_user_func_array([$this->redis, $func], $argv);
    }

    /**
     * 回调
     * 
     * @param \swoole_redis $client
     * @param mixed $result
     */
    public function callback(\swoole_redis $client, $result)
    {
        $callback = $this->callback;
        $this->callback = null;
        $callback($result);
    }

    /**
     * 魔术调用创建coro指令
     * 
     * @param string $func
     * @param array  $argv
     * @return array
     */
    public function __call($func, array $argv)
    {
        return new Task([$this, 'run'], [$func, $argv]);
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        $this->redis->close();
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        Debug::log(__METHOD__);
    }
}
