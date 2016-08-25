<?php

/*
 * PHP version 5.6
 */
namespace Core;

/**
 * 服务器控制中心
 * 
 * @author  Corz<combo_k@126.com>
 * @version 1.0
 */
class Server
{

    protected static $instance = null;

    /**
     * TCP服务器端
     * @var \swoole_server
     */
    protected $serv = null;

    /**
     * 构造
     */
    protected function __construct()
    {
        $conf = Conf::get('swoole.');
        $set = $conf['set'];
        $mode = $conf['mode'];
        if ($conf['pack_type'] == 'eof') {
            $set['open_eof_check'] = true;
            $set['open_eof_split'] = true;
            empty($set['package_eof']) && $set['package_eof'] = "\r\n\r\n";
        } else {
            $set['open_length_check'] = true;
            $set['package_length_type'] = Packet::HEADER_PACK;
            $set['package_length_offset'] = 0;
            $set['package_body_offset'] = Packet::HEADER_SIZE;
        }
        $ip = $conf['ip'];
        $port = $conf['port'];
        
        $shortopts = 'h:p:d';
        $longopts = array('user:', 'group:', 'worker_num:', 'log_file:', 'pid_file:');
        $opt = getopt($shortopts, $longopts);
        if (! empty($opt)) {
            ! empty($opt['h']) && $ip = $opt['h'];
            ! empty($opt['p']) && $port = $opt['p'];
            isset($opt['d']) && $set['daemonize'] = true;
            ! empty($opt['user']) && $set['user'] = $opt['user'];
            ! empty($opt['group']) && $set['group'] = $opt['group'];
            ! empty($opt['worker_num']) && $set['worker_num'] = $opt['worker_num'];
            ! empty($opt['log_file']) && $set['log_file'] = $opt['log_file'];
        }
        
        $this->serv = new \swoole_server($ip, $port, $mode);
        $this->serv->on('receive', [$this, 'onReceive']);
        //pid文件
        if (! empty($opt['pid_file'])) {
            $this->serv->on('start', 
                function () use($opt) {
                    file_put_contents($opt['pid_file'], posix_getpid());
                });
        }
        $this->serv->set($set);
    }

    /**
     * 获取实例
     * 
     * @return self
     */
    public static function getInstance()
    {
        return isset(self::$instance) ? self::$instance : (self::$instance = new self());
    }

    /**
     * 运行服务
     */
    public function run()
    {
        $this->serv->start();
    }

    /**
     * tcp接收数据
     * 
     * @param \swoole_server $serv
     * @param int $fd
     * @param int $from_id
     * @param string $data
     */
    public function onReceive(\swoole_server $serv, $fd, $from_id, $data)
    {
        Debug::log("\n" . str_repeat('-', 50), "\n" . posix_getpid(), memory_get_usage(), __METHOD__, " FD:$fd FROM:$from_id");
        Packet::decode($data);
        $requester = new Requester($fd, $data);
        try {
            $data = json_decode($requester->getData(), true);
            $method = ucfirst($data['method']);
            $className = "App\\{$method}";
            if (! class_exists($className)) {
                throw new \Exception('class not exists');
            }
            $instance = new $className($requester);
            $gen = $instance->run($data);
            //generator方法
            if ($gen) {
                Coroutine::create($gen);
            }
        } catch (\Exception $e) {
            $requester->send(json_encode(['status' => 500, 'info' => $e->getMessage()]));
        }
    }

    /**
     * 发送数据
     * 
     * @param int $fd
     * @param string $data
     * @return boolean
     */
    public function send($fd, $data)
    {
        return $this->serv->send($fd, Packet::encode($data));
    }

    /**
     * 关闭连接
     *
     * @param int $fd
     * @return boolean
     */
    public function close($fd)
    {
        return $this->serv->close($fd);
    }
}