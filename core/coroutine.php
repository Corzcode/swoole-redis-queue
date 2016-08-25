<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 生成器容器
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Coroutine
{

    /**
     * @var \Generator
     */
    protected $generator = null;

    /**
     * 执行协程
     * 
     * @param \Generator $gen
     */
    public static function create(\Generator $gen)
    {
        $instance = new self($gen);
        $instance->run();
    }

    public function __construct(\Generator $gen)
    {
        Debug::log(__METHOD__);
        $this->generator = $gen;
    }

    public function run()
    {
        $task = $this->generator->current();
        if (empty($task)) {
            $this->generator = null;
            return;
        }
        $argv = $task->argv;
        array_unshift($argv, [$this, 'callback']);
        call_user_func_array($task->callback, $argv);
    }

    public function callback($result)
    {
        $this->generator->send($result);
        $this->run();
    }

    public function __destruct()
    {
        Debug::log(__METHOD__);
    }
}