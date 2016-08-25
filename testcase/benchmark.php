<?php
/*
 * PHP version 5.6
 */
include dirname(__DIR__) . '/client/mq_client.php';
$options = getopt("n:c:h:p:");
if (empty($options['n']) || empty($options['c'])) {
    exit('use : php ' . $argv[0] . ' -n 100 -c 10' . "\n");
}

$ip = empty($options['h']) ? '127.0.0.1' : $options['h'];
$port = empty($options['p']) ? '9570' : $options['p'];

$table = new swoole_table(2 << strlen(decbin($options['n'])));
$table->column('time', swoole_table::TYPE_INT, 5);
$table->column('push', swoole_table::TYPE_INT, 5);
$table->column('pop', swoole_table::TYPE_INT, 5);
$table->column('ack', swoole_table::TYPE_INT, 5);
$table->create();

$allc = intval($options['n']);
$allp = intval($options['c']);
$count = intval($options['n'] / $options['c']);
$callback_function = function (\swoole_process $worker) use($count, $table, $ip, $port) {
    $pid = posix_getpid();
    $mq = MqClient::getInstance(['ip' => $ip, 'port' => $port]);
    $mq->setQueue('sms');
    for ($i = 0; $i < $count; $i ++) {
        $t = $ts = microtime(true);
        
        $r1 = $mq->push('asdfasdg');
        $time1 = intval((microtime(true) - $ts) * 1000);
        $ts = microtime(true);
        
        $r2 = $mq->pop();
        $time2 = intval((microtime(true) - $ts) * 1000);
        $ts = microtime(true);
        
        $r3 = $mq->ack($r2['id']);
        $time3 = intval((microtime(true) - $ts) * 1000);
        $time = (microtime(true) - $t) * 1000;
        $table->set("p{$pid}c{$i}", ['time' => $time, 'push' => $time1, 'pop' => $time2, 'ack' => $time3]);
        echo "pid {$pid}\t {$r2['id']} time:{$time} {$time1} {$time2} {$time3}\n";
    }
};

$worker = [];
$worker_num = $options['c']; //创建的进程数
for ($i = 0; $i < $worker_num; $i ++) {
    $worker[$i] = new \swoole_process($callback_function);
    $pid = $worker[$i]->start();
}
foreach ($worker as $i => $process) {
    $process->wait();
}

$atime = $pushtime = $poptime = $acktime = ['all' => 0, 'max' => 0];
foreach ($table as $k => $v) {
    $atime['time'] += $v['time'];
    $atime['max'] = $v['time'] > $atime['max'] ? $v['time'] : $atime['max'];
    $pushtime['time'] += $v['push'];
    $pushtime['max'] = $v['push'] > $pushtime['max'] ? $v['push'] : $pushtime['max'];
    $poptime['time'] += $v['pop'];
    $poptime['max'] = $v['pop'] > $poptime['max'] ? $v['pop'] : $poptime['max'];
    $acktime['time'] += $v['ack'];
    $acktime['max'] = $v['ack'] > $acktime['max'] ? $v['ack'] : $acktime['max'];
}

$qps = intval(1000 / ($atime['time'] / $allc) * $allp);
echo "\n";
echo "Count:$allc Process:$allp QPS:$qps\n";
echo "All time " . $atime['time'] . "ms  avg:" . $atime['time'] / $allc . "ms  max:{$atime['max']}ms\n";
echo "Push time " . $pushtime['time'] . "ms  avg:" . $pushtime['time'] / $allc . "ms  max:{$pushtime['max']}ms\n";
echo "Pop time " . $poptime['time'] . "ms  avg:" . $poptime['time'] / $allc . "ms  max:{$poptime['max']}ms\n";
echo "Ack time " . $acktime['time'] . "ms  avg:" . $acktime['time'] / $allc . "ms  max:{$acktime['max']}ms\n";

