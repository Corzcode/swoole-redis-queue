<?php
/*
 * PHP version 5.6
 */
include dirname(__DIR__) . '/client/mq_client.php';

$mq = MqClient::getInstance(['ip' => '127.0.0.1', 'port' => 9570]);

$mq->setQueue('sms');

if (empty($argv[1]) || $argv[1] == 'push') {
    $r = $mq->push('asdfasdg');
    var_dump($r);
}
if (empty($argv[1]) || $argv[1] == 'pop') {
    if (! empty($argv[2])) {
        $mq->block(intval($argv[2]));
    }
    $r = $mq->pop();
    var_dump($r);
}
if (empty($argv[1]) || $argv[1] == 'ack') {
    $id = empty($argv[2]) ? $r['id'] : trim($argv[2]);
    $r = $mq->ack($id);
    var_dump($r);
}