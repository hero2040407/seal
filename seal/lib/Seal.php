<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/15
 * Time: 15:02
 */
namespace  seal;

use Swoole\Http\Response;
use swoole_websocket_server as websocket;

class Seal
{
    public $ser;

    public function __construct()
    {
        $this->ser = new websocket("0.0.0.0", 9501);
        $this->ser->set(array('task_worker_num' => 4));
        $this->ser->on('start', [$this, 'start']);
        $this->ser->on('open', [$this, 'open']);
        $this->ser->on('message', [$this, 'onMessage']);
        $this->ser->on('close', [$this, 'onClose']);
        $this->ser->on('request', [$this, 'onRequest']);
        //处理异步任务的结果
        $this->ser->on('task', [$this, 'onTask']);
        $this->ser->on('finish', [$this, 'onFinish']);
        $this->ser->start();
    }

    public function start($server)
    {
        echo "服务启动  http://0.0.0.0:9501",PHP_EOL;
    }

    public function open(websocket $ws, $request) {
        $push = Push::getInstance($ws);
        $push->addClient($request->fd, $request->get['uid']);
        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "hello, welcome\n");
    }

    public function onMessage(websocket $ws, $frame) {
        echo "Message: {$frame->data}\n";
        var_dump($frame->data);
        $push = Push::getInstance($ws);
        $push->send($frame);
        $push->getAllClient();
    }

    public function onClose($ws, $fd) {
        $push = Push::getInstance($ws);
        $push->deleteClient($fd);
        echo "client-{$fd} is closed\n";
    }

    public function onTask(websocket $ws, $task_id, $from_id, $data) {
        echo "New AsyncTask[id=$task_id]".PHP_EOL;
        //返回任务执行的结果
        $ws->finish("$data -> OK");
    }

    public function onRequest($request, Response $response) {
        SealKernel::getInstance()->http($request,$response);
    }

    public function onFinish($ws, $task_id, $data) {
        echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
    }
//监听WebSocket连接关闭事件
}

new seal();