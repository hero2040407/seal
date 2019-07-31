<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/7/30
 * Time: 下午1:48
 */

namespace seal;

use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\NodeManager\RedisManager;

class RpcSeal {

    private static $instance;
    private $config;
    private $rpc;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static(new Config());
        }
        return self::$instance;
    }

    public function config()
    {
        $config = $this->config;
        $config->setServerIp('127.0.0.1');
        $config->setNodeManager(new RedisManager('127.0.0.1'));
        $config->getBroadcastConfig()->setEnableBroadcast(false);
        $config->getBroadcastConfig()->setEnableListen(false);
        $config->getBroadcastConfig()->setSecretKey('zhongguo');
        $this->rpc = new Rpc($this->config);
    }

    public function rpc() :Rpc
    {
        return $this->rpc;
    }

    public function generate()
    {
        $list = $this->rpc->generateProcess();
        foreach ($list['worker'] as $p) {
            $p->getProcess()->start();
        }

        foreach ($list['tickWorker'] as $p) {
            $p->getProcess()->start();
        }
    }
}



