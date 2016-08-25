# swoole-redis-queue
![Supported PHP versions: >=5.5](https://img.shields.io/badge/php-%3E%3D5.5-blue.svg)
![Supported SWOOLE versions: >=1.8.10](https://img.shields.io/badge/swoole-%3E%3D1.8.10-orange.svg)
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