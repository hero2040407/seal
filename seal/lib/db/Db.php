<?php
/**
 * Created by IntelliJ IDEA.
 * User: alex
 * Date: 2019/3/28
 * Time: 下午3:00
 */
namespace seal\db;

use Swoole\Coroutine\Mysql;
use Swoole\Coroutine\Channel;

class Db
{
    private $chan;
    private static $instance;

    public function __construct($size)
    {
        $this->chan = new Channel($size);
        $this->generateConnections($size);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static(100);
        }
        return self::$instance;
    }

    public function generateConnections($size)
    {
        for ($i = 0; $i < $size; $i++) {
            $mysql = new Mysql();
            $mysql->connect(\seal\Config::getInstance()->get('database'));
            $this->chan->push($mysql);
        }
    }

    public function getConnection() :Mysql
    {
        return $this->chan->pop();
    }

    public function close($mysql)
    {
        $this->chan->push($mysql);
    }

    public function getPoolLength()
    {
        return $this->chan->length();
    }
}