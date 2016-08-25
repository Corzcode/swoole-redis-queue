#!/bin/sh

PHPBIN=/path/to/php
PHPCLICONF=/path/to/php.cli.ini

script_dir=$( cd $(dirname $0); pwd)
pidfile="$script_dir/logs/server.pid"


case "$1" in
  start)
     echo "Starting Swoole Redis Mq server"
     $PHPBIN -c $PHPCLICONF $script_dir/server.php --pid_file=$pidfile -d
     ;;
  stop)
     PID=`cat "$pidfile"`
     echo "Stopping Swoole Redis Mq server"
     if [ ! -z "$PID" ]; then
        kill -15 $PID
     fi
     ;;
  restart)
     $0 stop
     $0 start
     ;;
  *)
     echo "Usage: $0 {start|stop|restart}"
     exit 1
esac
exit 0
