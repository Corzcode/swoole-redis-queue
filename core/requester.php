<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 与应用层交互请求者
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Requester
{

    /**
     * @var int
     */
    protected $fd = null;

    /**
     * @var string
     */
    protected $data = null;

    /**
     * 构造
     * 
     * @param int $fd
     * @param string $data
     */
    public function __construct($fd, $data)
    {
        Debug::log(__METHOD__);
        $this->fd = $fd;
        $this->data = $data;
    }

    /**
     * 获取数据
     *
     * @return boolean
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 发送数据
     *
     * @return boolean
     */
    public function send($data)
    {
        return Server::getInstance()->send($this->fd, $data);
    }

    /**
     * 关闭连 
     * 
     * @return boolean
     */
    public function close()
    {
        return Server::getInstance()->close($this->fd);
    }

    public function __destruct()
    {
        Debug::log(__METHOD__);
    }
}