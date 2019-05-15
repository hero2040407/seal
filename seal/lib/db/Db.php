<?php
/**
 * Created by IntelliJ IDEA.
 * UserModel: alex
 * Date: 2019/3/28
 * Time: 下午3:00
 */
namespace seal\db;

use Swoole\Coroutine\Mysql;
use Swoole\Coroutine\Channel;

class Db
{
    use \seal\db\drive\Mysql;

    private $chan;
    private static $instance;

    public function __construct($size)
    {
        $this->chan = new Channel($size);
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
        $mysql = $this->chan->pop(0.001);
        if (!$mysql) {
            $mysql = new Mysql();
            $mysql->connect(\seal\Config::getInstance()->get('database'));
        }

        return $mysql;
    }

    public function close($mysql)
    {
        $this->chan->push($mysql);
    }

    public function getPoolLength()
    {
        return $this->chan->length();
    }

    public function setConnection()
    {
        $mysql = new Mysql();
        $mysql->connect(\seal\Config::getInstance()->get('database'));
        $this->chan->push($mysql);
    }

    public function achieve()
    {
        $mysql = $this->getConnection();
        try {
            $arr = $mysql->query($this->sql);
            if ($arr === false)
            {
                var_dump($mysql->errno, $mysql->error);
            }
            $this->close($mysql);
            return $arr;
        } catch (\Exception $e) {
            $this->setConnection();
            return $this->achieve();
        }
    }
}