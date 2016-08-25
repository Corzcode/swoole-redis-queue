<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 协程任务
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Task
{

    /**
     * @var callable
     */
    public $callback = null;

    /**
     * @var array
     */
    public $argv = array();

    public function __construct($callback, $argv = array())
    {
        $this->callback = $callback;
        $this->argv = $argv;
    }
}