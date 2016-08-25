# swoole-redis-queue
![Supported PHP versions: >=5.5](http://php.net/)
![Supported SWOOLE versions: >=1.8.10](https://github.com/swoole/swoole-src)
启动方式
------
```
php server.php
```

守护方式启动
------
```
./cli.sh start
./cli.sh stop
./cli.sh restart
```


客户端使用
------
```
$mq = MqClient::getInstance(['ip' => '127.0.0.1', 'port' => 9570]);
$mq->setQueue('sms');
$mq->push('asdfasdg');
$r = $mq->pop();
$mq->ack($r['id']);
```