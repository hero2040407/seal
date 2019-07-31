<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/7/31
 * Time: 上午10:43
 */

namespace seal\db;


use seal\Request;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine\MySQL;

class MysqlPool
{

    private $chan;
    private static $instance;

    public function __construct($size)
    {
        $this->chan = new Channel($size);
    }

    public static function getInstance() :MysqlPool
    {
        $module = Request::getInstance()->getModule();
        if (!isset(self::$instance[$module])) {
            self::$instance[$module] = new static(100);
        }
        return self::$instance[$module];
    }

    public function getConnection(): MySQL
    {
        $mysql = $this->chan->pop(0.001);
        if (!$mysql) {
            $this->setConnection();
        }
        else
            return $mysql;

        return $this->getConnection();
    }

    public function close(Db $db)
    {
        $this->chan->push($db->connection);
        unset($db);
    }

    public function getPoolLength()
    {
        return $this->chan->length();
    }

    public function setConnection()
    {
        $module = Request::getInstance()->getModule();
        $connect = new MySQL();
        $connect->connect(\seal\Config::getInstance()->get('database.' . $module));
        $this->chan->push($connect);
    }
}