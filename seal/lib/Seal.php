<?php
/**
 * Created by PhpStorm.
 * UserModel: LENOVO
 * Date: 2019/1/15
 * Time: 15:02
 */

namespace seal;

use app\rpc\test\UserService;
use Swoole\Http\Response;
use swoole_websocket_server as websocket;

class Seal
{
    private $ser;
    private $config = array();
    public $name;
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
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
        $this->name = $config['name'];
        $this->setProcessTitle($this->name . '-manager');
        return $this;
    }

    public function start($server)
    {
        $this->setProcessTitle($this->name . '-master');
        echo "服务启动  http://0.0.0.0:9501", PHP_EOL;
    }

    public function run()
    {
        Push::getInstance();
        $rpcSeal = RpcSeal::getInstance();
        $rpcSeal->config();
        $rpcSeal->rpc()->add(new UserService());
        $rpcSeal->generate();
//        Db::setConfig(Config::getInstance()->get('database'));
        $swoole_server = isset($this->config['server']) && $this->config['server'] == 'websocket' ? 'swoole_websocket_server' : 'swoole_http_server';

        $this->ser = new $swoole_server($this->config['ip'], $this->config['port']);

        $this->ser->set($this->config['set']);

        $this->ser->on('start', [$this, 'start']);
        $this->ser->on('WorkerStart', [$this, 'onWorkerStart']);
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
        $model = Push::getInstance();
        $model->set($request->get['uid'], ['fd' => $request->fd]);
//        $ws->bind($request->get['uid'], $request->fd);
//        $push->addClient($request->fd, $request->get['uid']);
//        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "hello, welcome\n");
    }

    public function onMessage(websocket $ws, $frame)
    {
//        echo "Message: {$frame->data}\n";
//        var_dump($frame->data);
//        $push = Push::getInstance($ws);
//        $push->getAllClient();
        Kernel::getInstance()->websocket($ws, $frame);
    }

    public function onClose(websocket $ws, $fd)
    {
        Push::getInstance()->deleteClient($fd);
//        var_dump(Push::getInstance()->count());
//        echo "client-{$fd} is closed\n";
    }

    public function onWorkerStart($server, $workder_id)
    {
//        go(function (){
//            Db::getInstance();
//        });
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        // 重新加载配置
        $this->reloadConfig();
        if (!$server->taskworker) {//worker进程
            $this->setProcessTitle($this->name . "-worker");
        } else {
            $this->setProcessTitle($this->name . "-tasker");
        }
        swoole_timer_tick(3000, function ($time_id) {
            Log::getInstance()->save();
        });
    }

    public function onTask(websocket $ws, $task)
    {
        return Task::getInstance()->setServer($ws)->dispatch($task->id, $task->worker_id, $task->data);
    }

    public function onRequest($request, Response $response)
    {
        Kernel::getInstance()->http($this->ser, $request, $response);
    }

    public function onFinish($ws, $task_id, $data)
    {
        Task::getInstance()->setServer($ws)->finish($task_id, $data);
    }

    /**
     * 重新加载配置
     */
    public function reloadConfig()
    {
        $this->config = Config::getInstance()->get('app');
        $this->name = $this->config['name'];
    }

    /**
     * Set process name.
     * @param string $title
     * @return void
     */
    public function setProcessTitle($title)
    {
        if (PHP_OS === 'Darwin') return;
        // >=php 5.5
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } else {
            @swoole_set_process_name($title);
        }
    }
//监听WebSocket连接关闭事件
}