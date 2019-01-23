<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2019/1/15
 * Time: 15:02
 */

namespace seal;

use Swoole\Http\Response;
use swoole_websocket_server as websocket;

class Seal
{
    private $ser;

    private $config = array();
    private static $instance;

//    public function __construct()
//    {
//        $this->ser = new websocket("0.0.0.0", 9501);
//        $this->ser->set(array('task_worker_num' => 4));
//        $this->ser->on('start', [$this, 'start']);
//        $this->ser->on('open', [$this, 'open']);
//        $this->ser->on('message', [$this, 'onMessage']);
//        $this->ser->on('close', [$this, 'onClose']);
//        $this->ser->on('request', [$this, 'onRequest']);
//        //处理异步任务的结果
//        $this->ser->on('task', [$this, 'onTask']);
//        $this->ser->on('finish', [$this, 'onFinish']);
//        $this->ser->start();
//    }

    private function __construct( ){ }

    public static function getInstance(){
        if(is_null (self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 配置信息赋值
     * @param $config
     * @return $this
     * @throws
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function start($server)
    {
        echo "服务启动  http://0.0.0.0:9501", PHP_EOL;
    }

    public function run()
    {
        $swoole_server = isset($this->config['server']) && $this->config['server'] == 'websocket' ? 'swoole_websocket_server' : 'swoole_http_server';

        $this->ser = new $swoole_server($this->config['ip'], $this->config['port']);

        $this->ser->set($this->config['set']);

        $this->ser->on('start', [$this, 'start']);
//        $this->ser->on('WorkerStart', [$this, 'onWorkerStart']);
        if ($this->config['server'] == 'websocket') {
            $this->ser->on('open', [$this, 'open']);
            $this->ser->on('message', [$this, 'onMessage']);
            $this->ser->on('close', [$this, 'onClose']);
        }

        if (isset($this->config['set']['task_worker_num']) && $this->config['set']['task_worker_num'] > 0) {
            $this->ser->on('task', [$this, 'onTask']);
            $this->ser->on('finish', [$this, 'onFinish']);
        }
        $this->ser->on('request', [$this, 'onRequest']);
        $this->ser->start();
    }

    public function open(websocket $ws, $request)
    {
        $push = Push::getInstance($ws);
        $push->addClient($request->fd, $request->get['uid']);
        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "hello, welcome\n");
    }

    public function onMessage(websocket $ws, $frame)
    {
        echo "Message: {$frame->data}\n";
        var_dump($frame->data);
        $push = Push::getInstance($ws);
        $push->send($frame);
        $push->getAllClient();
    }

    public function onClose($ws, $fd)
    {
        $push = Push::getInstance($ws);
        $push->deleteClient($fd);
        echo "client-{$fd} is closed\n";
    }

    public function onTask(websocket $ws, $task_id, $from_id, $data)
    {
        return Task::getInstance()->setServer($ws)->dispatch($task_id, $from_id, $data);
    }

    public function onRequest($request, Response $response)
    {
        SealKernel::getInstance()->http($this->ser, $request, $response);
    }

    public function onFinish($ws, $task_id, $data)
    {
        Task::getInstance()->setServer($ws)->finsh($task_id, $data);
    }
//监听WebSocket连接关闭事件
}